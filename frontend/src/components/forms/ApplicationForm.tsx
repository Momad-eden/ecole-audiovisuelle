"use client";

import Link from "next/link";
import { useRouter } from "next/navigation";
import { useEffect, useState } from "react";
import { useFieldArray, useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import type { Offering } from "@/lib/types";
import { fcfa, cn } from "@/lib/utils";
import { Field, Honeypot, inputClass } from "./Field";

const PHONE = /^\+?[0-9][0-9 ().-]{7,19}$/;
const DIPLOMAS = { CPS: "CPS — Certificat de Professionnalisation Spécialisée", CS: "CS — Certificat de Spécialité", CAP: "CAP / BEP", BFEM: "BFEM", "Baccalauréat": "Baccalauréat", BTS: "BTS / DUT (Bac+2)", Licence: "Licence", Autre: "Autre" };
const DOCUMENT_TYPES = { diploma: "Diplôme (CPS, CS…)", id_card: "Pièce d'identité", cv: "CV", experience_certificate: "Attestation d'expérience", portfolio: "Portfolio", cover_letter: "Lettre de motivation", other: "Autre" };

const schema = z.object({
  offeringId: z.string().min(1, "Choisissez une formation."),
  firstName: z.string().trim().min(1, "Indiquez votre prénom.").max(100),
  lastName: z.string().trim().min(1, "Indiquez votre nom.").max(100),
  gender: z.enum(["female", "male", ""]).optional(),
  birthDate: z.string().optional(),
  birthPlace: z.string().max(150).optional(),
  nationality: z.string().max(100).optional(),
  phone: z.string().trim().regex(PHONE, "Numéro invalide (ex. +221 77 123 45 67)."),
  whatsapp: z.string().trim().regex(PHONE, "Numéro invalide.").or(z.literal("")).optional(),
  email: z.string().trim().email("Adresse e-mail invalide.").or(z.literal("")).optional(),
  address: z.string().max(255).optional(),
  lastDiploma: z.string().optional(),
  diplomaYear: z.string().optional(),
  school: z.string().max(255).optional(),
  experience: z.array(z.object({ period: z.string().max(60), organization: z.string().max(150), role: z.string().max(150) })).max(15),
  motivation: z.string().max(3000).optional(),
  portfolioUrl: z.string().url("Lien invalide.").or(z.literal("")).optional(),
  documents: z.array(z.object({ type: z.string(), file: z.custom<FileList>() })).max(12),
  consent: z.literal(true, { message: "Votre accord est nécessaire pour traiter votre candidature." }),
  website: z.string().optional(),
});

type Values = z.infer<typeof schema>;

const STEPS = ["Formation", "Identité", "Coordonnées", "Parcours", "Envoi"];

export function ApplicationForm({ offerings, audience, preselected }: { offerings: Offering[]; audience: "school" | "professional"; preselected?: string }) {
  const router = useRouter();
  const draftKey = `emsi-candidature-${audience}`;
  const [step, setStep] = useState(0);
  const [serverError, setServerError] = useState<string | null>(null);
  const professional = audience === "professional";

  const form = useForm<Values>({
    resolver: zodResolver(schema),
    mode: "onTouched",
    defaultValues: {
      offeringId: preselected ?? (offerings.length === 1 ? String(offerings[0].id) : ""),
      nationality: "Sénégalaise", gender: "", whatsapp: "", email: "", portfolioUrl: "",
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
      if (saved) reset({ ...form.getValues(), ...JSON.parse(saved), documents: form.getValues("documents"), consent: undefined as never });
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

  const stepFields: (keyof Values)[][] = [
    ["offeringId"],
    ["firstName", "lastName", "gender", "birthDate"],
    ["phone", "whatsapp", "email"],
    ["lastDiploma", "portfolioUrl"],
    ["consent"],
  ];

  const next = async () => {
    if (await trigger(stepFields[step])) setStep((s) => Math.min(s + 1, STEPS.length - 1));
  };

  const onSubmit = async (values: Values) => {
    setServerError(null);
    if (professional && !values.documents.some((d) => d.type === "diploma" && d.file?.length)) {
      setError("documents", { message: "Joignez la copie de votre CPS ou CS." });
      setStep(3);
      return;
    }

    const body = new FormData();
    const append = (key: string, value: string | undefined | null) => value && body.append(key, value);
    append("offeringId", values.offeringId);
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

    const response = await fetch("/api/v1/public/applications", { method: "POST", body, headers: { Accept: "application/json" } });

    if (response.status === 201) {
      const { data } = (await response.json()) as { data: { reference: string | null } };
      try { localStorage.removeItem(draftKey); } catch {}
      router.push(`/candidater/confirmation?ref=${encodeURIComponent(data.reference ?? "")}`);
      return;
    }
    if (response.status === 422) {
      const { errors: fieldErrors } = (await response.json()) as { errors: Record<string, string[]> };
      setServerError(Object.values(fieldErrors).flat()[0] ?? "Certaines informations sont invalides.");
      return;
    }
    setServerError(response.status === 429 ? "Trop d'envois depuis cet appareil. Réessayez dans quelques minutes." : "L'envoi a échoué. Réessayez dans quelques minutes.");
  };

  const selected = offerings.find((o) => String(o.id) === watch("offeringId"));

  return (
    <form onSubmit={handleSubmit(onSubmit)} noValidate className="relative">
      <Honeypot register={register("website")} />
      <ol className="mb-10 flex flex-wrap gap-2" aria-label="Étapes">
        {STEPS.map((label, i) => (
          <li key={label} aria-current={i === step ? "step" : undefined} className={cn("rounded-full border px-4 py-1.5 text-sm", i === step ? "border-brand text-brand" : i < step ? "border-line text-ink" : "border-line text-ink-muted")}>
            {i + 1}. {label}
          </li>
        ))}
      </ol>

      <div className="rounded-3xl border border-line bg-night-2 p-6 sm:p-10">
        {step === 0 && (
          <fieldset className="space-y-4">
            <legend className="mb-6 font-display text-3xl">Quelle formation vous intéresse ?</legend>
            {errors.offeringId && <p role="alert" className="text-sm text-rec">{errors.offeringId.message}</p>}
            {offerings.map((offering) => (
              <label key={offering.id} className="flex cursor-pointer items-start gap-4 rounded-2xl border border-line p-5 has-[:checked]:border-brand">
                <input type="radio" value={String(offering.id)} className="mt-1 size-4 accent-brand" {...register("offeringId")} />
                <span>
                  <span className="block font-medium">{offering.label}</span>
                  <span className="text-sm text-ink-muted">
                    {offering.capacity ? `${offering.capacity} places · ` : ""}
                    {offering.feeAmount > 0 ? fcfa(offering.feeAmount) : offering.fundingLabel}
                  </span>
                </span>
              </label>
            ))}
          </fieldset>
        )}

        {step === 1 && (
          <fieldset className="grid gap-6 sm:grid-cols-2">
            <legend className="mb-6 font-display text-3xl">Votre identité</legend>
            <Field id="firstName" label="Prénom" required error={errors.firstName?.message}><input id="firstName" autoComplete="given-name" className={inputClass} aria-invalid={!!errors.firstName} {...register("firstName")} /></Field>
            <Field id="lastName" label="Nom" required error={errors.lastName?.message}><input id="lastName" autoComplete="family-name" className={inputClass} aria-invalid={!!errors.lastName} {...register("lastName")} /></Field>
            <Field id="gender" label="Genre"><select id="gender" className={inputClass} {...register("gender")}><option value="">Préfère ne pas répondre</option><option value="female">Femme</option><option value="male">Homme</option></select></Field>
            <Field id="birthDate" label="Date de naissance"><input id="birthDate" type="date" autoComplete="bday" className={inputClass} {...register("birthDate")} /></Field>
            <Field id="birthPlace" label="Lieu de naissance"><input id="birthPlace" className={inputClass} {...register("birthPlace")} /></Field>
            <Field id="nationality" label="Nationalité"><input id="nationality" autoComplete="country-name" className={inputClass} {...register("nationality")} /></Field>
          </fieldset>
        )}

        {step === 2 && (
          <fieldset className="grid gap-6 sm:grid-cols-2">
            <legend className="mb-6 font-display text-3xl">Comment vous joindre ?</legend>
            <Field id="phone" label="Téléphone" required error={errors.phone?.message} hint="Ex. +221 77 123 45 67"><input id="phone" type="tel" autoComplete="tel" className={inputClass} aria-invalid={!!errors.phone} {...register("phone")} /></Field>
            <Field id="whatsapp" label="WhatsApp (si différent)" error={errors.whatsapp?.message}><input id="whatsapp" type="tel" className={inputClass} {...register("whatsapp")} /></Field>
            <Field id="email" label="E-mail" error={errors.email?.message} hint="Pour recevoir l'accusé de réception."><input id="email" type="email" autoComplete="email" className={inputClass} aria-invalid={!!errors.email} {...register("email")} /></Field>
            <Field id="address" label="Adresse"><input id="address" autoComplete="street-address" className={inputClass} {...register("address")} /></Field>
          </fieldset>
        )}

        {step === 3 && (
          <div className="space-y-10">
            <fieldset className="grid gap-6 sm:grid-cols-3">
              <legend className="mb-6 font-display text-3xl">Votre parcours</legend>
              <Field id="lastDiploma" label="Dernier diplôme" required={professional}><select id="lastDiploma" className={inputClass} {...register("lastDiploma")}><option value="">Choisir</option>{Object.entries(DIPLOMAS).map(([v, l]) => <option key={v} value={v}>{l}</option>)}</select></Field>
              <Field id="diplomaYear" label="Année d'obtention"><input id="diplomaYear" inputMode="numeric" className={inputClass} {...register("diplomaYear")} /></Field>
              <Field id="school" label="Établissement"><input id="school" className={inputClass} {...register("school")} /></Field>
            </fieldset>

            {professional && (
              <fieldset>
                <legend className="mb-4 text-lg font-medium">Expérience professionnelle</legend>
                <div className="space-y-4">
                  {experience.fields.map((field, i) => (
                    <div key={field.id} className="grid gap-3 rounded-2xl border border-line p-4 sm:grid-cols-[1fr_1fr_1fr_auto]">
                      <input aria-label="Période" placeholder="Période (ex. 2024)" className={inputClass} {...register(`experience.${i}.period`)} />
                      <input aria-label="Structure ou événement" placeholder="Structure ou événement" className={inputClass} {...register(`experience.${i}.organization`)} />
                      <input aria-label="Rôle" placeholder="Rôle (ex. technicien son)" className={inputClass} {...register(`experience.${i}.role`)} />
                      <button type="button" onClick={() => experience.remove(i)} className="rounded-full px-3 text-sm text-ink-muted hover:text-rec">Retirer</button>
                    </div>
                  ))}
                </div>
                {experience.fields.length < 15 && <button type="button" onClick={() => experience.append({ period: "", organization: "", role: "" })} className="mt-4 rounded-full border border-line px-5 py-2 text-sm">+ Ajouter une expérience</button>}
              </fieldset>
            )}

            <fieldset>
              <legend className="mb-2 text-lg font-medium">Pièces justificatives {professional && <span className="text-brand">*</span>}</legend>
              <p className="mb-4 text-sm text-ink-muted">PDF, JPG ou PNG, 10 Mo maximum par fichier.{professional && " La copie du CPS ou du CS est obligatoire."}</p>
              {errors.documents?.message && <p role="alert" className="mb-3 text-sm text-rec">{errors.documents.message}</p>}
              <div className="space-y-3">
                {documents.fields.map((field, i) => (
                  <div key={field.id} className="grid gap-3 rounded-2xl border border-line p-4 sm:grid-cols-[14rem_1fr_auto]">
                    <select aria-label="Type de pièce" className={inputClass} {...register(`documents.${i}.type`)}>{Object.entries(DOCUMENT_TYPES).map(([v, l]) => <option key={v} value={v}>{l}</option>)}</select>
                    <input aria-label="Fichier" type="file" accept=".pdf,.jpg,.jpeg,.png" className="text-sm file:mr-4 file:rounded-full file:border-0 file:bg-night-3 file:px-4 file:py-2 file:text-ink" {...register(`documents.${i}.file`)} />
                    <button type="button" onClick={() => documents.remove(i)} className="rounded-full px-3 text-sm text-ink-muted hover:text-rec">Retirer</button>
                  </div>
                ))}
              </div>
              {documents.fields.length < 12 && <button type="button" onClick={() => documents.append({ type: "other", file: undefined as unknown as FileList })} className="mt-4 rounded-full border border-line px-5 py-2 text-sm">+ Ajouter une pièce</button>}
            </fieldset>

            <Field id="portfolioUrl" label="Lien vers vos réalisations (facultatif)" error={errors.portfolioUrl?.message}><input id="portfolioUrl" type="url" placeholder="https://" className={inputClass} {...register("portfolioUrl")} /></Field>
          </div>
        )}

        {step === 4 && (
          <div className="space-y-8">
            <h2 className="font-display text-3xl">Dernière étape</h2>
            <Field id="motivation" label="Pourquoi cette formation ? (facultatif)"><textarea id="motivation" rows={6} maxLength={3000} className={inputClass} {...register("motivation")} /></Field>
            {selected && <p className="rounded-2xl border border-line p-4 text-sm"><span className="cartel block">Formation choisie</span>{selected.label}</p>}
            <Field id="consent" label="" error={errors.consent?.message}>
              <label className="flex items-start gap-3 text-sm text-ink-muted">
                <input id="consent" type="checkbox" className="mt-1 size-4 accent-brand" {...register("consent")} />
                <span>J&apos;accepte que l&apos;EMSI traite ces informations pour étudier ma candidature et me contacter. Elles ne sont utilisées qu&apos;à cette fin. Voir la page <Link href="/confidentialite" className="text-brand underline">Protection des données</Link>.</span>
              </label>
            </Field>
            {serverError && <p role="alert" className="text-sm text-rec">{serverError}</p>}
          </div>
        )}
      </div>

      <div className="mt-8 flex justify-between gap-4">
        <button type="button" onClick={() => setStep((s) => Math.max(0, s - 1))} className={cn("min-h-12 rounded-full border border-line px-6", step === 0 && "invisible")}>Retour</button>
        {step < STEPS.length - 1 ? (
          <button type="button" onClick={next} className="min-h-12 rounded-full bg-brand px-8 font-semibold text-night">Continuer</button>
        ) : (
          <button type="submit" disabled={isSubmitting} className="min-h-12 rounded-full bg-brand px-8 font-semibold text-night disabled:opacity-60">{isSubmitting ? "Envoi en cours…" : "Envoyer ma candidature"}</button>
        )}
      </div>
    </form>
  );
}
