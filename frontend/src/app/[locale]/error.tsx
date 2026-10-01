"use client";

import { useT } from "@/components/i18n/LocaleProvider";

export default function Error({ reset }: { error: Error; reset: () => void }) {
  const t = useT().errors;
  return (
    <div className="mx-auto max-w-3xl px-4 pb-32 pt-44 text-center sm:px-6">
      <p className="cartel">{t.eyebrow}</p>
      <h1 className="display mt-4 text-[clamp(2.2rem,5vw,4rem)] text-balance">{t.title}</h1>
      <p className="mt-4 text-ink-muted">{t.text}</p>
      <button type="button" onClick={reset} className="mt-10 min-h-12 rounded-full bg-brand px-8 font-semibold text-on-accent">{t.retry}</button>
    </div>
  );
}
