"use client";

import { LocaleLink } from "@/components/i18n/LocaleLink";
import { useLocale, useT } from "@/components/i18n/LocaleProvider";
import { useRouter } from "next/navigation";
import { useEffect, useMemo, useState } from "react";
import { useFieldArray, useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import type { Dictionary } from "@/lib/i18n";
import { formatMoney } from "@/lib/i18n/format";
import type { Offering, Place } from "@/lib/types";
import { localizedPath } from "@/lib/i18n/locales";
import { campusLabel, offeringsForCampus, wantedOffering } from "@/lib/application";
import { cn } from "@/lib/utils";
import { Field, Honeypot, inputClass } from "./Field";

const PHONE = /^\+?[0-9][0-9 ().-]{7,19}$/;

/** Validation côté navigateur, messages dans la langue de la page (Laravel revalide et fait foi). */
const applicationSchema = ({ validation: v }: Dictionary) => z.object({
  offeringId: z.string().min(1, v.program),
  placeId: z.string().optional(),
  firstName: z.string().trim().min(1, v.firstName).max(100),
  lastName: z.string().trim().min(1, v.name).max(100),
  gender: z.enum(["female", "male", ""]).optional(),
  birthDate: z.string().optional(),
  birthPlace: z.string().max(150).optional(),
  nationality: z.string().max(100).optional(),
  phone: z.string().trim().regex(PHONE, v.phone),
  whatsapp: z.string().trim().regex(PHONE, v.phoneShort).or(z.literal("")).optional(),
  email: z.string().trim().email(v.email).or(z.literal("")).optional(),
  address: z.string().max(255).optional(),
  lastDiploma: z.string().optional(),
  diplomaYear: z.string().optional(),
  school: z.string().max(255).optional(),
  experience: z.array(z.object({ period: z.string().max(60), organization: z.string().max(150), role: z.string().max(150) })).max(15),
  motivation: z.string().max(3000).optional(),
  portfolioUrl: z.string().url(v.url).or(z.literal("")).optional(),
  documents: z.array(z.object({ type: z.string(), file: z.custom<FileList>() })).max(12),
  consent: z.literal(true, { message: v.applicationConsent }),
  website: z.string().optional(),
});

type Values = z.infer<ReturnType<typeof applicationSchema>>;

export function ApplicationForm({ offerings, audience, preselected, campuses = [], preselectedCampus, notice, wanted = [] }: { offerings: Offering[]; audience: "school" | "professional"; preselected?: string; campuses?: Place[]; preselectedCampus?: string; notice?: string; wanted?: number[] }) {
  const router = useRouter();
  const locale = useLocale();
  const dictionary = useT();
  const t = dictionary.application;
  const { forms: f } = dictionary;
  const schema = useMemo(() => applicationSchema(dictionary), [dictionary]);
  const STEPS = t.steps;
  const draftKey = `emsi-candidature-${audience}`;
  const [step, setStep] = useState(0);
  const [serverError, setServerError] = useState<string | null>(null);
  const [campusNotice, setCampusNotice] = useState<string | undefined>(notice);
  // Formation demandée depuis sa fiche : gardée jusqu'à ce que le candidat en choisisse une autre.
  const [wantedIds, setWantedIds] = useState<number[]>(wanted);
  const professional = audience === "professional";
  const chooseCampus = campuses.length > 1;

  const form = useForm<Values>({
    resolver: zodResolver(schema),
    mode: "onTouched",
    defaultValues: {
      offeringId: preselected ?? (offerings.length === 1 && !chooseCampus ? String(offerings[0].id) : ""),
      placeId: preselectedCampus ?? (campuses.length === 1 ? String(campuses[0].id) : ""),
      nationality: t.defaultNationality, gender: "", whatsapp: "", email: "", portfolioUrl: "",
      experience: [], documents: professional ? [{ type: "diploma", file: undefined as unknown as FileList }, { type: "id_card", file: undefined as unknown as FileList }] : [],
    },
  });
  const { register, handleSubmit, trigger, formState: { errors, isSubmitting }, watch, reset, setError } = form;
  const experience = useFieldArray({ control: form.control, name: "experience" });
  const documents = useFieldArray({ control: form.control, name: "documents" });

  // Brouillon conservé sur l'appareil (sans les fichiers) pour reprendre plus tard.
  useEffect(() => {
    try {
      const saved = localStorage.getItem(draftKey);
      if (saved) {
        const current = form.getValues();
        // Les choix venus de l'adresse (campus, formation) priment sur le brouillon.
        reset({ ...current, ...JSON.parse(saved), ...(preselectedCampus ? { placeId: preselectedCampus } : {}), ...(preselected ? { offeringId: preselected } : {}), documents: current.documents, consent: undefined as never });
      }
    } catch {}
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);
  useEffect(() => {
    const subscription = watch((values) => {
      try {
        const draft: Record<string, unknown> = { ...values };
        delete draft.documents;
        delete draft.consent;
        delete draft.website;
        localStorage.setItem(draftKey, JSON.stringify(draft));
      } catch {}
    });
    return () => subscription.unsubscribe();
  }, [watch, draftKey]);

  // Une formation choisie qui n'existe pas dans le campus retenu est retirée ; la formation demandée
  // depuis sa fiche est resélectionnée dès qu'un campus qui la propose est choisi (message sinon).
  const placeId = watch("placeId");
  const offeringId = watch("offeringId");
  const visibleOfferings = chooseCampus ? offeringsForCampus(offerings, placeId) : offerings;
  useEffect(() => {
    if (!chooseCampus) return;
    const kept = !!offeringId && visibleOfferings.some((o) => String(o.id) === offeringId);
    if (kept || !placeId) {
      if (offeringId && !kept) form.setValue("offeringId", "");
      return;
    }
    const wantedHere = wantedOffering({ offerings, campuses, campusId: placeId, wantedIds, notOffered: t.notOffered });
    form.setValue("offeringId", wantedHere.offeringId ?? "");
    if (wantedHere.notice) setCampusNotice(wantedHere.notice);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [placeId, offeringId]);

  const stepFields: (keyof Values)[][] = [
    ["offeringId"],
    ["firstName", "lastName", "gender", "birthDate"],
    ["phone", "whatsapp", "email"],
    ["lastDiploma", "portfolioUrl"],
    ["consent"],
  ];

  const next = async () => {
    // Plusieurs campus : le candidat choisit d'abord le sien, puis une formation qui y est proposée.
    if (step === 0 && chooseCampus && !watch("placeId")) {
      setError("placeId", { message: t.chooseCampus });
      return;
    }
    const valid = await trigger(stepFields[step]);
    if (valid) setStep((s) => Math.min(s + 1, STEPS.length - 1));
  };

  const onSubmit = async (values: Values) => {
    setServerError(null);
    if (professional && !values.documents.some((d) => d.type === "diploma" && d.file?.length)) {
      setError("documents", { message: t.diplomaMissing });
      setStep(3);
      return;
    }

    const body = new FormData();
    const append = (key: string, value: string | undefined | null) => value && body.append(key, value);
    append("offeringId", values.offeringId);
    append("placeId", values.placeId);
    append("firstName", values.firstName);
    append("lastName", values.lastName);
    append("gender", values.gender);
    append("birthDate", values.birthDate);
    append("birthPlace", values.birthPlace);
    append("nationality", values.nationality);
    append("phone", values.phone);
    append("whatsapp", values.whatsapp);
    append("email", values.email);
    append("address", values.address);
    append("education[lastDiploma]", values.lastDiploma);
    append("education[year]", values.diplomaYear);
    append("education[school]", values.school);
    values.experience.forEach((item, i) => {
      append(`experience[${i}][period]`, item.period);
      append(`experience[${i}][organization]`, item.organization);
      append(`experience[${i}][role]`, item.role);
    });
    append("motivation", values.motivation);
    append("portfolioUrl", values.portfolioUrl);
    let index = 0;
    values.documents.forEach((doc) => {
      const file = doc.file?.[0];
      if (file) {
        body.append(`documents[${index}][type]`, doc.type);
        body.append(`documents[${index}][file]`, file);
        index++;
      }
    });
    body.append("consent", "1");
    append("website", values.website);

    const response = await fetch(`/api/v1/public/applications?locale=${locale}`, { method: "POST", body, headers: { Accept: "application/json" } });

    if (response.status === 201) {
      const { data } = (await response.json()) as { data: { reference: string | null } };
      try { localStorage.removeItem(draftKey); } catch {}
      router.push(localizedPath(`/candidater/confirmation?ref=${encodeURIComponent(data.reference ?? "")}`, locale));
      return;
    }
    if (response.status === 422) {
      const { errors: fieldErrors } = (await response.json()) as { errors: Record<string, string[]> };
      // Formation ou campus refusés : l'erreur s'affiche sur le champ concerné, à la première étape.
      const onStepOne = (["offeringId", "placeId"] as const).find((field) => fieldErrors[field]?.length);
      if (onStepOne) {
        setError(onStepOne, { message: fieldErrors[onStepOne][0] });
        setStep(0);
        return;
      }
      setServerError(Object.values(fieldErrors).flat()[0] ?? t.invalid);
      return;
    }
    setServerError(response.status === 429 ? t.tooMany : f.failed);
  };

  const selected = offerings.find((o) => String(o.id) === watch("offeringId"));

  return (
    <form onSubmit={handleSubmit(onSubmit)} noValidate className="relative">
      <Honeypot register={register("website")} label={f.honeypot} />
      <ol className="mb-10 flex flex-wrap gap-2" aria-label={t.stepsLabel}>
        {STEPS.map((label, i) => (
          <li key={label} aria-current={i === step ? "step" : undefined} className={cn("rounded-full border px-4 py-1.5 text-sm", i === step ? "border-brand text-brand" : i < step ? "border-line text-ink" : "border-line text-ink-muted")}>
            {i + 1}. {label}
          </li>
        ))}
      </ol>

      <div className="rounded-3xl border border-line bg-night-2 p-6 sm:p-10">
        {step === 0 && (
          <div className="space-y-10">
            {chooseCampus && (
              <fieldset>
                <legend className="display mb-6 text-2xl sm:text-3xl">{t.campusLegend}</legend>
                {errors.placeId && <p role="alert" className="mb-3 text-sm text-rec">{errors.placeId.message}</p>}
                <div className="grid gap-3 sm:grid-cols-2">
                  {campuses.map((campus) => (
                    <label key={campus.id} className="flex cursor-pointer items-start gap-4 rounded-2xl border border-line p-5 has-[:checked]:border-brand">
                      <input type="radio" value={String(campus.id)} className="mt-1 size-4 accent-brand" {...register("placeId", { onChange: () => setCampusNotice(undefined) })} />
                      <span><span className="block font-medium">{campusLabel(campus)}</span><span className="text-sm text-ink-muted">{campus.address ?? campus.name}</span></span>
                    </label>
                  ))}
                </div>
              </fieldset>
            )}
            <fieldset className="space-y-4">
              <legend className="display mb-6 text-2xl sm:text-3xl">{t.programLegend}</legend>
              {campusNotice && <p role="status" className="rounded-2xl border border-line p-4 text-sm">{campusNotice}</p>}
              {errors.offeringId && <p role="alert" className="text-sm text-rec">{errors.offeringId.message}</p>}
              {chooseCampus && !placeId && <p className="text-sm text-ink-muted">{t.chooseCampusFirst}</p>}
              {chooseCampus && placeId && visibleOfferings.length === 0 && <p className="text-sm text-ink-muted">{t.noneInCampus}</p>}
              {visibleOfferings.map((offering) => (
                <label key={offering.id} className="flex cursor-pointer items-start gap-4 rounded-2xl border border-line p-5 has-[:checked]:border-brand">
                  <input type="radio" value={String(offering.id)} className="mt-1 size-4 accent-brand" {...register("offeringId", { onChange: (e) => { setCampusNotice(undefined); if (!wantedIds.includes(Number(e.target.value))) setWantedIds([]); } })} />
                  <span>
                    <span className="block font-medium">{offering.label}</span>
                    <span className="text-sm text-ink-muted">
                      {offering.capacity ? t.seats(offering.capacity) : ""}
                      {offering.feeAmount > 0 ? formatMoney(offering.feeAmount, locale) : offering.fundingLabel}
                    </span>
                  </span>
                </label>
              ))}
            </fieldset>
          </div>
        )}

        {step === 1 && (
          <fieldset className="grid gap-6 sm:grid-cols-2">
            <legend className="display mb-6 text-2xl sm:text-3xl">{t.identityLegend}</legend>
            <Field id="firstName" label={t.firstName} required error={errors.firstName?.message}><input id="firstName" autoComplete="given-name" className={inputClass} aria-invalid={!!errors.firstName} {...register("firstName")} /></Field>
            <Field id="lastName" label={t.lastName} required error={errors.lastName?.message}><input id="lastName" autoComplete="family-name" className={inputClass} aria-invalid={!!errors.lastName} {...register("lastName")} /></Field>
            <Field id="gender" label={t.gender}><select id="gender" className={inputClass} {...register("gender")}><option value="">{t.genderNone}</option><option value="female">{t.female}</option><option value="male">{t.male}</option></select></Field>
            <Field id="birthDate" label={t.birthDate}><input id="birthDate" type="date" autoComplete="bday" className={inputClass} {...register("birthDate")} /></Field>
            <Field id="birthPlace" label={t.birthPlace}><input id="birthPlace" className={inputClass} {...register("birthPlace")} /></Field>
            <Field id="nationality" label={t.nationality}><input id="nationality" autoComplete="country-name" className={inputClass} {...register("nationality")} /></Field>
          </fieldset>
        )}

        {step === 2 && (
          <fieldset className="grid gap-6 sm:grid-cols-2">
            <legend className="display mb-6 text-2xl sm:text-3xl">{t.contactLegend}</legend>
            <Field id="phone" label={f.phone} required error={errors.phone?.message} hint={t.phoneHint}><input id="phone" type="tel" autoComplete="tel" className={inputClass} aria-invalid={!!errors.phone} {...register("phone")} /></Field>
            <Field id="whatsapp" label={t.whatsapp} error={errors.whatsapp?.message}><input id="whatsapp" type="tel" className={inputClass} {...register("whatsapp")} /></Field>
            <Field id="email" label={f.email} error={errors.email?.message} hint={t.emailHint}><input id="email" type="email" autoComplete="email" className={inputClass} aria-invalid={!!errors.email} {...register("email")} /></Field>
            <Field id="address" label={t.address}><input id="address" autoComplete="street-address" className={inputClass} {...register("address")} /></Field>
          </fieldset>
        )}

        {step === 3 && (
          <div className="space-y-10">
            <fieldset className="grid gap-6 sm:grid-cols-3">
              <legend className="display mb-6 text-2xl sm:text-3xl">{t.backgroundLegend}</legend>
              <Field id="lastDiploma" label={t.lastDiploma} required={professional}><select id="lastDiploma" className={inputClass} {...register("lastDiploma")}><option value="">{t.choose}</option>{Object.entries(t.diplomas).map(([v, l]) => <option key={v} value={v}>{l}</option>)}</select></Field>
              <Field id="diplomaYear" label={t.diplomaYear}><input id="diplomaYear" inputMode="numeric" className={inputClass} {...register("diplomaYear")} /></Field>
              <Field id="school" label={t.school}><input id="school" className={inputClass} {...register("school")} /></Field>
            </fieldset>

            {professional && (
              <fieldset>
                <legend className="mb-4 text-lg font-medium">{t.experienceLegend}</legend>
                <div className="space-y-4">
                  {experience.fields.map((field, i) => (
                    <div key={field.id} className="grid gap-3 rounded-2xl border border-line p-4 sm:grid-cols-[1fr_1fr_1fr_auto]">
                      <input aria-label={t.period} placeholder={t.periodPlaceholder} className={inputClass} {...register(`experience.${i}.period`)} />
                      <input aria-label={t.organization} placeholder={t.organization} className={inputClass} {...register(`experience.${i}.organization`)} />
                      <input aria-label={t.role} placeholder={t.rolePlaceholder} className={inputClass} {...register(`experience.${i}.role`)} />
                      <button type="button" onClick={() => experience.remove(i)} className="rounded-full px-3 text-sm text-ink-muted hover:text-rec">{t.remove}</button>
                    </div>
                  ))}
                </div>
                {experience.fields.length < 15 && <button type="button" onClick={() => experience.append({ period: "", organization: "", role: "" })} className="mt-4 rounded-full border border-line px-5 py-2 text-sm">{t.addExperience}</button>}
              </fieldset>
            )}

            <fieldset>
              <legend className="mb-2 text-lg font-medium">{t.documentsLegend} {professional && <span className="text-brand">*</span>}</legend>
              <p className="mb-4 text-sm text-ink-muted">{t.documentsHint}{professional && t.diplomaRequired}</p>
              {errors.documents?.message && <p role="alert" className="mb-3 text-sm text-rec">{errors.documents.message}</p>}
              <div className="space-y-3">
                {documents.fields.map((field, i) => (
                  <div key={field.id} className="grid gap-3 rounded-2xl border border-line p-4 sm:grid-cols-[14rem_1fr_auto]">
                    <select aria-label={t.documentType} className={inputClass} {...register(`documents.${i}.type`)}>{Object.entries(t.documentTypes).map(([v, l]) => <option key={v} value={v}>{l}</option>)}</select>
                    <input aria-label={t.file} type="file" accept=".pdf,.jpg,.jpeg,.png" className="text-sm file:mr-4 file:rounded-full file:border-0 file:bg-night-3 file:px-4 file:py-2 file:text-ink" {...register(`documents.${i}.file`)} />
                    <button type="button" onClick={() => documents.remove(i)} className="rounded-full px-3 text-sm text-ink-muted hover:text-rec">{t.remove}</button>
                  </div>
                ))}
              </div>
              {documents.fields.length < 12 && <button type="button" onClick={() => documents.append({ type: "other", file: undefined as unknown as FileList })} className="mt-4 rounded-full border border-line px-5 py-2 text-sm">{t.addDocument}</button>}
            </fieldset>

            <Field id="portfolioUrl" label={t.portfolio} error={errors.portfolioUrl?.message}><input id="portfolioUrl" type="url" placeholder="https://" className={inputClass} {...register("portfolioUrl")} /></Field>
          </div>
        )}

        {step === 4 && (
          <div className="space-y-8">
            <h2 className="display text-2xl sm:text-3xl">{t.lastStep}</h2>
            <Field id="motivation" label={t.motivation}><textarea id="motivation" rows={6} maxLength={3000} className={inputClass} {...register("motivation")} /></Field>
            {selected && <p className="rounded-2xl border border-line p-4 text-sm"><span className="cartel block">{t.chosen}</span>{selected.label}</p>}
            <Field id="consent" label="" error={errors.consent?.message}>
              <label className="flex items-start gap-3 text-sm text-ink-muted">
                <input id="consent" type="checkbox" className="mt-1 size-4 accent-brand" {...register("consent")} />
                <span>{t.consentBefore}<LocaleLink href="/confidentialite" className="text-brand underline">{t.consentLink}</LocaleLink>{t.consentAfter}</span>
              </label>
            </Field>
            {serverError && <p role="alert" className="text-sm text-rec">{serverError}</p>}
          </div>
        )}
      </div>

      <div className="mt-8 flex justify-between gap-4">
        <button type="button" onClick={() => setStep((s) => Math.max(0, s - 1))} className={cn("min-h-12 rounded-full border border-line px-6", step === 0 && "invisible")}>{t.back}</button>
        {step < STEPS.length - 1 ? (
          <button type="button" onClick={next} className="min-h-12 rounded-full bg-brand px-8 font-semibold text-on-accent">{t.next}</button>
        ) : (
          <button type="submit" disabled={isSubmitting} className="min-h-12 rounded-full bg-brand px-8 font-semibold text-on-accent disabled:opacity-60">{isSubmitting ? t.submitting : t.submit}</button>
        )}
      </div>
    </form>
  );
}
