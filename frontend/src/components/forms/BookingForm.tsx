"use client";

import Link from "next/link";
import { useState } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { Minus, Plus, Trash2 } from "lucide-react";
import { useQuote } from "@/components/quote/useQuote";
import type { BookingType } from "@/lib/types";
import { Field, Honeypot, inputClass } from "./Field";

export const BOOKING_TYPES: Record<BookingType, string> = {
  studio_session: "Session au studio",
  equipment_rental: "Location de matériel",
  event_service: "Prestation pour un événement",
  space_rental: "Location de l'Espace Habib Faye",
};

const PHONE = /^\+?[0-9][0-9 ().-]{7,19}$/;

const schema = z
  .object({
    type: z.enum(Object.keys(BOOKING_TYPES) as [BookingType, ...BookingType[]]),
    name: z.string().trim().min(2, "Indiquez votre nom.").max(150),
    organization: z.string().trim().max(150).optional(),
    phone: z.string().trim().regex(PHONE, "Numéro invalide (ex. +221 77 123 45 67)."),
    email: z.string().trim().email("Adresse e-mail invalide.").or(z.literal("")),
    startsOn: z.string().optional(),
    endsOn: z.string().optional(),
    location: z.string().trim().max(255).optional(),
    attendees: z.string().regex(/^\d*$/, "Indiquez un nombre.").optional(),
    message: z.string().trim().max(3000).optional(),
    consent: z.literal(true, { message: "Votre accord est nécessaire pour que nous puissions vous répondre." }),
    website: z.string().optional(),
  })
  .refine((v) => !v.startsOn || !v.endsOn || v.endsOn >= v.startsOn, { message: "La date de fin doit suivre la date de début.", path: ["endsOn"] });

type Values = z.infer<typeof schema>;

/** Demande de devis ou de réservation (studio, matériel, prestation, salle), avec la sélection « Ma demande ». */
export function BookingForm({ type = "equipment_rental" }: { type?: BookingType }) {
  const quote = useQuote();
  const [result, setResult] = useState<{ reference: string | null } | null>(null);
  const [error, setError] = useState<string | null>(null);
  const today = new Date().toISOString().slice(0, 10);
  const { register, handleSubmit, watch, setError: setFieldError, formState: { errors, isSubmitting } } = useForm<Values>({
    resolver: zodResolver(schema),
    defaultValues: { type, email: "", attendees: "" },
  });
  const selectedType = watch("type");
  const withItems = selectedType === "equipment_rental" || selectedType === "event_service";

  const onSubmit = async ({ attendees, ...values }: Values) => {
    setError(null);
    const response = await fetch("/api/v1/public/booking-requests", {
      method: "POST",
      headers: { "Content-Type": "application/json", Accept: "application/json" },
      body: JSON.stringify({
        ...values,
        attendees: attendees ? Number(attendees) : null,
        startsOn: values.startsOn || null,
        endsOn: values.endsOn || null,
        items: withItems ? quote.items.map(({ kind, id, quantity }) => ({ kind, id, quantity })) : [],
      }),
    });

    if (response.status === 201) {
      const body = await response.json();
      if (withItems) quote.clear();
      setResult({ reference: body.data.reference });
      return;
    }
    if (response.status === 422) {
      const body = await response.json();
      for (const [field, messages] of Object.entries<string[]>(body.errors ?? {})) {
        if (field.startsWith("items")) setError("Un élément de votre sélection n'est plus disponible : retirez-le puis renvoyez la demande.");
        else setFieldError(field as keyof Values, { message: messages[0] });
      }
      return;
    }
    setError(response.status === 429 ? "Trop d'envois en peu de temps. Réessayez dans quelques minutes." : "L'envoi a échoué. Réessayez dans quelques minutes, ou appelez-nous.");
  };

  if (result) {
    return (
      <div role="status" className="rounded-3xl border border-line bg-night-2 p-10 text-center">
        <p className="display text-3xl">Demande envoyée !</p>
        {result.reference && <p className="mt-4 text-lg">Votre référence : <strong className="font-mono text-[var(--accent-ink)]">{result.reference}</strong></p>}
        <p className="mx-auto mt-3 max-w-lg text-ink-muted">Notre équipe vous recontacte rapidement avec une proposition. Gardez votre référence pour nos échanges.</p>
      </div>
    );
  }

  return (
    <form onSubmit={handleSubmit(onSubmit)} noValidate className="relative space-y-6 rounded-3xl border border-line bg-night-2 p-6 sm:p-10">
      <Honeypot register={register("website")} />

      <Field id="type" label="Votre demande" required>
        <select id="type" className={inputClass} {...register("type")}>
          {Object.entries(BOOKING_TYPES).map(([value, label]) => <option key={value} value={value}>{label}</option>)}
        </select>
      </Field>

      {withItems && (
        <div className="rounded-2xl border border-line p-5">
          <p className="cartel mb-3">Votre sélection</p>
          {quote.items.length === 0 ? (
            <p className="text-sm text-ink-muted">
              Aucun matériel choisi. <Link href="/maison-habib-faye" className="font-semibold text-[var(--accent-ink)] underline underline-offset-4">Parcourir le matériel</Link>, ou décrivez simplement votre besoin ci-dessous.
            </p>
          ) : (
            <ul className="divide-y divide-line">
              {quote.items.map((item) => (
                <li key={`${item.kind}-${item.id}`} className="flex flex-wrap items-center justify-between gap-3 py-3">
                  <span className="font-medium">{item.name}</span>
                  <span className="flex items-center gap-2">
                    <button type="button" className="grid size-9 place-items-center rounded-full border border-line" onClick={() => quote.setQuantity(item, item.quantity - 1)} aria-label={`Réduire la quantité de ${item.name}`}><Minus className="size-4" aria-hidden /></button>
                    <input type="number" min={1} max={999} value={item.quantity} onChange={(e) => quote.setQuantity(item, Number(e.target.value))} className="w-16 rounded-lg border border-line bg-night px-2 py-1.5 text-center tabular-nums" aria-label={`Quantité de ${item.name}`} />
                    <button type="button" className="grid size-9 place-items-center rounded-full border border-line" onClick={() => quote.setQuantity(item, item.quantity + 1)} aria-label={`Augmenter la quantité de ${item.name}`}><Plus className="size-4" aria-hidden /></button>
                    <button type="button" className="grid size-9 place-items-center rounded-full text-ink-muted hover:text-rec" onClick={() => quote.remove(item)} aria-label={`Retirer ${item.name}`}><Trash2 className="size-4" aria-hidden /></button>
                  </span>
                </li>
              ))}
            </ul>
          )}
        </div>
      )}

      <div className="grid gap-6 sm:grid-cols-2">
        <Field id="name" label="Nom" required error={errors.name?.message}>
          <input id="name" autoComplete="name" className={inputClass} aria-invalid={!!errors.name} {...register("name")} />
        </Field>
        <Field id="organization" label="Artiste, structure ou entreprise">
          <input id="organization" autoComplete="organization" className={inputClass} {...register("organization")} />
        </Field>
        <Field id="phone" label="Téléphone (WhatsApp de préférence)" required error={errors.phone?.message}>
          <input id="phone" type="tel" autoComplete="tel" placeholder="+221 77 000 00 00" className={inputClass} aria-invalid={!!errors.phone} {...register("phone")} />
        </Field>
        <Field id="email" label="E-mail" error={errors.email?.message}>
          <input id="email" type="email" autoComplete="email" className={inputClass} aria-invalid={!!errors.email} {...register("email")} />
        </Field>
        <Field id="startsOn" label="Date souhaitée (début)" error={errors.startsOn?.message}>
          <input id="startsOn" type="date" min={today} className={inputClass} {...register("startsOn")} />
        </Field>
        <Field id="endsOn" label="Date de fin" error={errors.endsOn?.message}>
          <input id="endsOn" type="date" min={today} className={inputClass} aria-invalid={!!errors.endsOn} {...register("endsOn")} />
        </Field>
        {selectedType !== "studio_session" && (
          <>
            <Field id="location" label="Lieu de l'événement">
              <input id="location" className={inputClass} placeholder="Ex. Saint-Louis, place Faidherbe" {...register("location")} />
            </Field>
            <Field id="attendees" label="Public attendu (personnes)" error={errors.attendees?.message}>
              <input id="attendees" inputMode="numeric" className={inputClass} aria-invalid={!!errors.attendees} {...register("attendees")} />
            </Field>
          </>
        )}
      </div>

      <Field id="message" label={selectedType === "studio_session" ? "Votre projet (titres, style, musiciens…)" : "Votre besoin"}>
        <textarea id="message" rows={5} className={inputClass} {...register("message")} />
      </Field>

      <Field id="consent" label="" error={errors.consent?.message}>
        <label className="flex items-start gap-3 text-sm text-ink-muted">
          <input id="consent" type="checkbox" className="mt-1 size-4 accent-brand" {...register("consent")} />
          J&apos;accepte que ces informations soient utilisées pour répondre à ma demande.
        </label>
      </Field>

      {error && <p role="alert" className="text-sm text-rec">{error}</p>}
      <button type="submit" disabled={isSubmitting} className="inline-flex min-h-12 items-center rounded-full bg-[var(--accent-ink)] px-8 font-semibold text-on-accent transition hover:brightness-110 disabled:opacity-60">
        {isSubmitting ? "Envoi…" : "Envoyer ma demande"}
      </button>
    </form>
  );
}
