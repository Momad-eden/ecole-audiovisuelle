"use client";

import { useT } from "@/components/i18n/LocaleProvider";
import { ButtonLink } from "@/components/ui/ButtonLink";

/** Contenu de la page 404 dans la langue de la page (not-found.tsx ne reçoit pas la langue en paramètre). */
export function NotFoundContent() {
  const { notFound: t, meta } = useT();
  return (
    <div className="beam mx-auto max-w-3xl px-4 pb-32 pt-44 text-center sm:px-6">
      <title>{`${meta.notFound} — EMSI`}</title>
      <p className="cartel">{t.eyebrow}</p>
      <h1 className="display mt-4 text-[clamp(2.4rem,6vw,4.5rem)] text-balance">{t.title}</h1>
      <p className="mt-4 text-ink-muted">{t.text}</p>
      <div className="mt-10 flex justify-center gap-3"><ButtonLink href="/emsi" variant="secondary">{t.universes}</ButtonLink><ButtonLink href="/">{t.home}</ButtonLink></div>
    </div>
  );
}
