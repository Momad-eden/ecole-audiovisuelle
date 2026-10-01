"use client";

import { useMemo, useState } from "react";
import { useLocale, useT } from "@/components/i18n/LocaleProvider";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { BOOKING_TYPES, publicBookingType } from "@/lib/booking";
import type { Dictionary } from "@/lib/i18n";
import type { BookingType } from "@/lib/types";
import { Field, Honeypot, inputClass } from "./Field";

const PHONE = /^\+?[0-9][0-9 ().-]{7,19}$/;

/** Validation côté navigateur, messages dans la langue de la page (Laravel revalide et fait foi). */
const bookingSchema = ({ validation: v }: Dictionary) =>
  z
    .object({
      type: z.enum(BOOKING_TYPES),
      name: z.string().trim().min(2, v.name).max(150),
      organization: z.string().trim().max(150).optional(),
      phone: z.string().trim().regex(PHONE, v.phone),
      email: z.string().trim().email(v.email).or(z.literal("")),
      startsOn: z.string().optional(),
      endsOn: z.string().optional(),
      location: z.string().trim().max(255).optional(),
      attendees: z.string().regex(/^\d*$/, v.number).optional(),
      message: z.string().trim().max(3000).optional(),
      consent: z.literal(true, { message: v.consent }),
      website: z.string().optional(),
    })
    .refine((values) => !values.startsOn || !values.endsOn || values.endsOn >= values.startsOn, { message: v.endAfterStart, path: ["endsOn"] });

type Values = z.infer<ReturnType<typeof bookingSchema>>;

/** Demande de réservation : séance au studio ou location d'un espace du Centre culturel. */
export function BookingForm({ type }: { type?: BookingType }) {
  const locale = useLocale();
  const t = useT();
  const { forms: f, booking: b } = t;
  const schema = useMemo(() => bookingSchema(t), [t]);
  const [result, setResult] = useState<{ reference: string | null } | null>(null);
  const [error, setError] = useState<string | null>(null);
  const today = new Date().toISOString().slice(0, 10);
  const { register, handleSubmit, watch, setError: setFieldError, formState: { errors, isSubmitting } } = useForm<Values>({
    resolver: zodResolver(schema),
    defaultValues: { type: publicBookingType(type), email: "", attendees: "" },
  });
  const selectedType = watch("type");

  const onSubmit = async ({ attendees, ...values }: Values) => {
    setError(null);
    const response = await fetch(`/api/v1/public/booking-requests?locale=${locale}`, {
      method: "POST",
      headers: { "Content-Type": "application/json", Accept: "application/json" },
      body: JSON.stringify({
        ...values,
        attendees: attendees ? Number(attendees) : null,
        startsOn: values.startsOn || null,
        endsOn: values.endsOn || null,
      }),
    });

    if (response.status === 201) {
      const body = await response.json();
      setResult({ reference: body.data.reference });
      return;
    }
    if (response.status === 422) {
      const body = await response.json();
      for (const [field, messages] of Object.entries<string[]>(body.errors ?? {})) {
        setFieldError(field as keyof Values, { message: messages[0] });
      }
      return;
    }
    setError(response.status === 429 ? b.tooMany : b.failed);
  };

  if (result) {
    return (
      <div role="status" className="rounded-3xl border border-line bg-night-2 p-10 text-center">
        <p className="display text-3xl">{b.sentTitle}</p>
        {result.reference && <p className="mt-4 text-lg">{b.reference}<strong className="font-mono text-[var(--accent-ink)]">{result.reference}</strong></p>}
        <p className="mx-auto mt-3 max-w-lg text-ink-muted">{b.sentText}</p>
      </div>
    );
  }

  return (
    <form onSubmit={handleSubmit(onSubmit)} noValidate className="relative space-y-6 rounded-3xl border border-line bg-night-2 p-6 sm:p-10">
      <Honeypot register={register("website")} label={f.honeypot} />

      <Field id="type" label={b.request} required>
        <select id="type" className={inputClass} {...register("type")}>
          {BOOKING_TYPES.map((value) => <option key={value} value={value}>{b.types[value]}</option>)}
        </select>
      </Field>

      <div className="grid gap-6 sm:grid-cols-2">
        <Field id="name" label={f.name} required error={errors.name?.message}>
          <input id="name" autoComplete="name" className={inputClass} aria-invalid={!!errors.name} {...register("name")} />
        </Field>
        <Field id="organization" label={b.organization}>
          <input id="organization" autoComplete="organization" className={inputClass} {...register("organization")} />
        </Field>
        <Field id="phone" label={b.phone} required error={errors.phone?.message}>
          <input id="phone" type="tel" autoComplete="tel" placeholder={f.phonePlaceholder} className={inputClass} aria-invalid={!!errors.phone} {...register("phone")} />
        </Field>
        <Field id="email" label={f.email} error={errors.email?.message}>
          <input id="email" type="email" autoComplete="email" className={inputClass} aria-invalid={!!errors.email} {...register("email")} />
        </Field>
        <Field id="startsOn" label={b.startsOn} error={errors.startsOn?.message}>
          <input id="startsOn" type="date" min={today} className={inputClass} {...register("startsOn")} />
        </Field>
        <Field id="endsOn" label={b.endsOn} error={errors.endsOn?.message}>
          <input id="endsOn" type="date" min={today} className={inputClass} aria-invalid={!!errors.endsOn} {...register("endsOn")} />
        </Field>
        {selectedType !== "studio_session" && (
          <>
            <Field id="location" label={b.location}>
              <input id="location" className={inputClass} placeholder={b.locationPlaceholder} {...register("location")} />
            </Field>
            <Field id="attendees" label={b.attendees} error={errors.attendees?.message}>
              <input id="attendees" inputMode="numeric" className={inputClass} aria-invalid={!!errors.attendees} {...register("attendees")} />
            </Field>
          </>
        )}
      </div>

      <Field id="message" label={selectedType === "studio_session" ? b.studioProject : b.need}>
        <textarea id="message" rows={5} className={inputClass} {...register("message")} />
      </Field>

      <Field id="consent" label="" error={errors.consent?.message}>
        <label className="flex items-start gap-3 text-sm text-ink-muted">
          <input id="consent" type="checkbox" className="mt-1 size-4 accent-brand" {...register("consent")} />
          {f.consent}
        </label>
      </Field>

      {error && <p role="alert" className="text-sm text-rec">{error}</p>}
      <button type="submit" disabled={isSubmitting} className="inline-flex min-h-12 items-center rounded-full bg-[var(--accent-ink)] px-8 font-semibold text-on-accent transition hover:brightness-110 disabled:opacity-60">
        {isSubmitting ? f.sending : b.submit}
      </button>
    </form>
  );
}
