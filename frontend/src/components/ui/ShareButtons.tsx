"use client";

import { useState } from "react";
import { Check, Link2 } from "lucide-react";
import { useLocale } from "@/components/i18n/LocaleProvider";
import { localizedPath } from "@/lib/i18n/locales";
import { siteUrl } from "@/lib/utils";

/** Partager une page : WhatsApp (premier réflexe au Sénégal), Facebook, ou copier le lien. */
export function ShareButtons({ path, title }: { path: string; title: string }) {
  const [copied, setCopied] = useState(false);
  const url = `${siteUrl}${localizedPath(path, useLocale())}`;
  const text = encodeURIComponent(`${title} — ${url}`);
  const pill = "inline-flex min-h-10 items-center gap-2 rounded-full border border-line px-4 text-sm transition hover:border-ink/40";

  return (
    <div className="flex flex-wrap items-center gap-2">
      <span className="cartel mr-1">Partager</span>
      <a className={pill} href={`https://wa.me/?text=${text}`} target="_blank" rel="noopener noreferrer">WhatsApp<span className="sr-only"> (nouvel onglet)</span></a>
      <a className={pill} href={`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`} target="_blank" rel="noopener noreferrer">Facebook<span className="sr-only"> (nouvel onglet)</span></a>
      <button
        type="button"
        className={pill}
        onClick={async () => {
          try {
            await navigator.clipboard.writeText(url);
            setCopied(true);
            setTimeout(() => setCopied(false), 2000);
          } catch {
            // Presse-papiers indisponible : le lien reste dans la barre d'adresse.
          }
        }}
      >
        {copied ? <Check className="size-4" aria-hidden /> : <Link2 className="size-4" aria-hidden />}
        {copied ? "Lien copié" : "Copier le lien"}
      </button>
    </div>
  );
}
