"use client";

import { useMemo, useState } from "react";
import { useLocale, useT } from "@/components/i18n/LocaleProvider";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import type { Dictionary } from "@/lib/i18n";
import { Field, Honeypot, inputClass } from "./Field";

const SUBJECTS = ["information", "partnership", "press", "visit", "other"] as const;

/** Validation côté navigateur, messages dans la langue de la page (Laravel revalide et fait foi). */
const contactSchema = ({ validation: v }: Dictionary) =>
  z
    .object({
      subject: z.enum(SUBJECTS),
      name: z.string().trim().min(2, v.name).max(150),
      email: z.string().trim().email(v.email).or(z.literal("")),
      phone: z.string().trim().max(30).optional(),
      message: z.string().trim().min(10, v.messageShort).max(3000),
      consent: z.literal(true, { message: v.consent }),
      website: z.string().optional(),
    })
    .refine((values) => values.email || values.phone, { message: v.emailOrPhone, path: ["email"] });

type Values = z.infer<ReturnType<typeof contactSchema>>;

export function ContactForm() {
  const locale = useLocale();
  const t = useT();
  const { forms: f, contactForm: c } = t;
  const schema = useMemo(() => contactSchema(t), [t]);
  const [status, setStatus] = useState<"idle" | "sent" | "error">("idle");
  const { register, handleSubmit, formState: { errors, isSubmitting } } = useForm<Values>({ resolver: zodResolver(schema), defaultValues: { subject: "information", email: "", phone: "" } });

  const onSubmit = async (values: Values) => {
    setStatus("idle");
    const response = await fetch(`/api/v1/public/contact-messages?locale=${locale}`, {
      method: "POST",
      headers: { "Content-Type": "application/json", Accept: "application/json" },
      body: JSON.stringify(values),
    });
    setStatus(response.ok ? "sent" : "error");
  };

  if (status === "sent") {
    return <div role="status" className="rounded-3xl border border-line bg-night-2 p-10 text-center"><p className="display text-3xl">{f.thanks}</p><p className="mt-3 text-ink-muted">{f.sent}</p></div>;
  }

  return (
    <form onSubmit={handleSubmit(onSubmit)} noValidate className="relative space-y-6 rounded-3xl border border-line bg-night-2 p-6 sm:p-10">
      <Honeypot register={register("website")} label={f.honeypot} />
      <Field id="subject" label={c.subject} required>
        <select id="subject" className={inputClass} {...register("subject")}>
          {SUBJECTS.map((value) => <option key={value} value={value}>{c.subjects[value]}</option>)}
        </select>
      </Field>
      <Field id="name" label={f.name} required error={errors.name?.message}>
        <input id="name" autoComplete="name" className={inputClass} aria-invalid={!!errors.name} {...register("name")} />
      </Field>
      <div className="grid gap-6 sm:grid-cols-2">
        <Field id="email" label={f.email} error={errors.email?.message}>
          <input id="email" type="email" autoComplete="email" className={inputClass} aria-invalid={!!errors.email} {...register("email")} />
        </Field>
        <Field id="phone" label={f.phone}>
          <input id="phone" type="tel" autoComplete="tel" placeholder={f.phonePlaceholder} className={inputClass} {...register("phone")} />
        </Field>
      </div>
      <Field id="message" label={f.message} required error={errors.message?.message}>
        <textarea id="message" rows={6} className={inputClass} aria-invalid={!!errors.message} {...register("message")} />
      </Field>
      <Field id="consent" label="" error={errors.consent?.message}>
        <label className="flex items-start gap-3 text-sm text-ink-muted">
          <input id="consent" type="checkbox" className="mt-1 size-4 accent-brand" {...register("consent")} />
          {c.consent}
        </label>
      </Field>
      {status === "error" && <p role="alert" className="text-sm text-rec">{f.failed}</p>}
      <button type="submit" disabled={isSubmitting} className="inline-flex min-h-12 items-center rounded-full bg-brand px-8 font-semibold text-on-accent disabled:opacity-60">
        {isSubmitting ? f.sending : f.send}
      </button>
    </form>
  );
}
