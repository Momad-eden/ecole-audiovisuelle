"use client";

import { useState } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { Field, Honeypot, inputClass } from "./Field";

const SUBJECTS = { information: "Demande d'information", partnership: "Partenariat", press: "Presse", visit: "Visite de l'école", other: "Autre" };

const schema = z
  .object({
    subject: z.enum(Object.keys(SUBJECTS) as [keyof typeof SUBJECTS, ...(keyof typeof SUBJECTS)[]]),
    name: z.string().trim().min(2, "Indiquez votre nom.").max(150),
    email: z.string().trim().email("Adresse e-mail invalide.").or(z.literal("")),
    phone: z.string().trim().max(30).optional(),
    message: z.string().trim().min(10, "Votre message est trop court.").max(3000),
    consent: z.literal(true, { message: "Votre accord est nécessaire pour que nous puissions vous répondre." }),
    website: z.string().optional(),
  })
  .refine((v) => v.email || v.phone, { message: "Indiquez un e-mail ou un téléphone.", path: ["email"] });

type Values = z.infer<typeof schema>;

export function ContactForm() {
  const [status, setStatus] = useState<"idle" | "sent" | "error">("idle");
  const { register, handleSubmit, formState: { errors, isSubmitting } } = useForm<Values>({ resolver: zodResolver(schema), defaultValues: { subject: "information", email: "", phone: "" } });

  const onSubmit = async (values: Values) => {
    setStatus("idle");
    const response = await fetch("/api/v1/public/contact-messages", {
      method: "POST",
      headers: { "Content-Type": "application/json", Accept: "application/json" },
      body: JSON.stringify(values),
    });
    setStatus(response.ok ? "sent" : "error");
  };

  if (status === "sent") {
    return <div role="status" className="rounded-3xl border border-line bg-night-2 p-10 text-center"><p className="font-display text-3xl">Merci !</p><p className="mt-3 text-ink-muted">Votre message a bien été envoyé. Nous vous répondrons rapidement.</p></div>;
  }

  return (
    <form onSubmit={handleSubmit(onSubmit)} noValidate className="relative space-y-6 rounded-3xl border border-line bg-night-2 p-6 sm:p-10">
      <Honeypot register={register("website")} />
      <Field id="subject" label="Objet" required>
        <select id="subject" className={inputClass} {...register("subject")}>
          {Object.entries(SUBJECTS).map(([value, label]) => <option key={value} value={value}>{label}</option>)}
        </select>
      </Field>
      <Field id="name" label="Nom" required error={errors.name?.message}>
        <input id="name" autoComplete="name" className={inputClass} aria-invalid={!!errors.name} {...register("name")} />
      </Field>
      <div className="grid gap-6 sm:grid-cols-2">
        <Field id="email" label="E-mail" error={errors.email?.message}>
          <input id="email" type="email" autoComplete="email" className={inputClass} aria-invalid={!!errors.email} {...register("email")} />
        </Field>
        <Field id="phone" label="Téléphone">
          <input id="phone" type="tel" autoComplete="tel" placeholder="+221 77 000 00 00" className={inputClass} {...register("phone")} />
        </Field>
      </div>
      <Field id="message" label="Message" required error={errors.message?.message}>
        <textarea id="message" rows={6} className={inputClass} aria-invalid={!!errors.message} {...register("message")} />
      </Field>
      <Field id="consent" label="" error={errors.consent?.message}>
        <label className="flex items-start gap-3 text-sm text-ink-muted">
          <input id="consent" type="checkbox" className="mt-1 size-4 accent-amber" {...register("consent")} />
          J&apos;accepte que l&apos;EMSI utilise ces informations pour répondre à ma demande.
        </label>
      </Field>
      {status === "error" && <p role="alert" className="text-sm text-rec">L&apos;envoi a échoué. Réessayez dans quelques minutes.</p>}
      <button type="submit" disabled={isSubmitting} className="inline-flex min-h-12 items-center rounded-full bg-amber px-8 font-semibold text-night disabled:opacity-60">
        {isSubmitting ? "Envoi…" : "Envoyer"}
      </button>
    </form>
  );
}
