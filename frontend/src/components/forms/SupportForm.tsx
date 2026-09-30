"use client";

import { useEffect, useRef, useState } from "react";
import { useLocale } from "@/components/i18n/LocaleProvider";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { Field, Honeypot, inputClass } from "./Field";

const SUPPORT_TYPES = { partnership: "Partenariat", sponsorship: "Mécénat", donation: "Don", other: "Autre" };
type SupportType = keyof typeof SUPPORT_TYPES;

const schema = z
  .object({
    name: z.string().trim().min(2, "Indiquez votre nom.").max(150),
    organization: z.string().trim().max(150).optional(),
    email: z.string().trim().email("Adresse e-mail invalide.").max(255).or(z.literal("")),
    phone: z.string().trim().max(30).optional(),
    supportType: z.enum(Object.keys(SUPPORT_TYPES) as [SupportType, ...SupportType[]], { message: "Choisissez un type de soutien." }),
    message: z.string().trim().min(10, "Votre message est trop court.").max(3000),
    consent: z.literal(true, { message: "Votre accord est nécessaire pour que nous puissions vous répondre." }),
    website: z.string().optional(),
  })
  .refine((v) => v.email || v.phone, { message: "Indiquez un e-mail ou un téléphone.", path: ["email"] });

type Values = z.infer<typeof schema>;

/** Formulaire « Nous soutenir » (partenariat, mécénat, don) : arrive dans les messages reçus de l'admin. */
export function SupportForm() {
  const locale = useLocale();
  const [status, setStatus] = useState<"idle" | "sent" | "error">("idle");
  const confirmation = useRef<HTMLDivElement>(null);
  const { register, handleSubmit, formState: { errors, isSubmitting } } = useForm<Values>({
    resolver: zodResolver(schema),
    defaultValues: { supportType: "partnership", email: "", phone: "", organization: "" },
  });

  useEffect(() => {
    if (status === "sent") confirmation.current?.focus();
  }, [status]);

  const onSubmit = async (values: Values) => {
    setStatus("idle");
    try {
      const response = await fetch(`/api/v1/public/support?locale=${locale}`, {
        method: "POST",
        headers: { "Content-Type": "application/json", Accept: "application/json" },
        body: JSON.stringify(values),
      });
      setStatus(response.ok ? "sent" : "error");
    } catch {
      setStatus("error");
    }
  };

  if (status === "sent") {
    return (
      <div ref={confirmation} tabIndex={-1} role="status" className="rounded-3xl border border-line bg-night-2 p-10 text-center focus:outline-none">
        <p className="display text-3xl">Merci !</p>{" "}
        <p className="mt-3 text-ink-muted">Votre message a bien été envoyé. Nous vous répondrons rapidement.</p>
      </div>
    );
  }

  const describedBy = (name: keyof Values) => (errors[name] ? `support-${name}-error` : undefined);

  return (
    <form onSubmit={handleSubmit(onSubmit)} noValidate className="relative space-y-6 rounded-3xl border border-line bg-night-2 p-6 sm:p-10">
      <Honeypot register={register("website")} />
      <div className="grid gap-6 sm:grid-cols-2">
        <Field id="support-name" label="Nom" required error={errors.name?.message}>
          <input id="support-name" autoComplete="name" className={inputClass} aria-invalid={!!errors.name} aria-describedby={describedBy("name")} {...register("name")} />
        </Field>
        <Field id="support-organization" label="Organisation">
          <input id="support-organization" autoComplete="organization" className={inputClass} {...register("organization")} />
        </Field>
        <Field id="support-email" label="E-mail" error={errors.email?.message}>
          <input id="support-email" type="email" autoComplete="email" className={inputClass} aria-invalid={!!errors.email} aria-describedby={describedBy("email")} {...register("email")} />
        </Field>
        <Field id="support-phone" label="Téléphone">
          <input id="support-phone" type="tel" autoComplete="tel" placeholder="+221 77 000 00 00" className={inputClass} {...register("phone")} />
        </Field>
      </div>
      <Field id="support-supportType" label="Type de soutien" required error={errors.supportType?.message}>
        <select id="support-supportType" className={inputClass} {...register("supportType")}>
          {Object.entries(SUPPORT_TYPES).map(([value, label]) => <option key={value} value={value}>{label}</option>)}
        </select>
      </Field>
      <Field id="support-message" label="Message" required error={errors.message?.message}>
        <textarea id="support-message" rows={6} className={inputClass} aria-invalid={!!errors.message} aria-describedby={describedBy("message")} {...register("message")} />
      </Field>
      <Field id="support-consent" label="" error={errors.consent?.message}>
        <label className="flex items-start gap-3 text-sm text-ink-muted">
          <input id="support-consent" type="checkbox" className="mt-1 size-4 accent-brand" aria-invalid={!!errors.consent} aria-describedby={describedBy("consent")} {...register("consent")} />
          J&apos;accepte que ces informations soient utilisées pour répondre à ma demande.
        </label>
      </Field>
      {status === "error" && <p role="alert" className="text-sm text-rec">L&apos;envoi a échoué. Réessayez dans quelques minutes.</p>}
      <button type="submit" disabled={isSubmitting} className="inline-flex min-h-12 items-center rounded-full bg-brand px-8 font-semibold text-on-accent disabled:opacity-60">
        {isSubmitting ? "Envoi…" : "Envoyer"}
      </button>
    </form>
  );
}
