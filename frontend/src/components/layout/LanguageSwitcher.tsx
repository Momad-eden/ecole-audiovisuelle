"use client";

import Link from "next/link";
import { usePathname, useRouter } from "next/navigation";
import { useLocale, useT } from "@/components/i18n/LocaleProvider";
import { delocalizedPath, LOCALES, localizedPath, type Locale } from "@/lib/i18n/locales";
import { cn } from "@/lib/utils";

/** Nom de chaque langue dans sa propre langue (identique quelle que soit la langue de la page). */
const NAMES: Record<Locale, string> = { fr: "Français", en: "English" };

export const LOCALE_COOKIE = "emsi-locale";

/** Choix mémorisé un an ; le site ne s'en sert jamais pour rediriger (seuls les liens FR · EN changent la langue). */
function remember(locale: Locale) {
  document.cookie = `${LOCALE_COOKIE}=${locale}; path=/; max-age=${60 * 60 * 24 * 365}; SameSite=Lax`;
}

/**
 * Sélecteur « FR · EN » : lien vers la même page dans l'autre langue. Les chemins sont identiques
 * dans les deux langues (/emsi/dakar ↔ /en/emsi/dakar), le lien se déduit donc de l'adresse courante.
 */
export function LanguageSwitcher({ className }: { className?: string }) {
  const current = useLocale();
  const t = useT();
  const router = useRouter();
  const path = delocalizedPath(usePathname() || "/");

  // Les paramètres de l'adresse (référence d'un dossier, campus choisi…) suivent le changement de langue.
  // Lus au clic plutôt qu'avec useSearchParams, qui imposerait un Suspense autour de l'en-tête.
  function onClick(event: React.MouseEvent<HTMLAnchorElement>, locale: Locale) {
    remember(locale);
    const { search, hash } = window.location;
    if (!(search || hash) || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
    event.preventDefault();
    router.push(localizedPath(path, locale) + search + hash);
  }

  return (
    <nav aria-label={t.header.language} className={className}>
      <ul className="flex items-center rounded-full border border-line p-1 text-xs font-semibold tracking-wider">
        {LOCALES.map((locale) => {
          const active = locale === current;
          return (
            <li key={locale}>
              <Link
                href={localizedPath(path, locale)}
                hrefLang={locale}
                lang={locale}
                aria-label={NAMES[locale]}
                aria-current={active ? "true" : undefined}
                prefetch={false}
                onClick={(event) => onClick(event, locale)}
                className={cn("grid min-h-9 min-w-10 place-items-center rounded-full px-2 transition", active ? "bg-ink text-night" : "text-ink/75 hover:text-ink")}
              >
                {locale.toUpperCase()}
              </Link>
            </li>
          );
        })}
      </ul>
    </nav>
  );
}
