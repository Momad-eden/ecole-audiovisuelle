"use client";

import { usePathname } from "next/navigation";
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
  const path = delocalizedPath(usePathname() || "/");

  // Lien classique (chargement complet) : la mise en page racine change avec la langue (<html lang>,
  // script du thème), une navigation côté client la re-rendrait. Les paramètres de l'adresse (référence
  // d'un dossier, campus choisi…) suivent, lus au clic plutôt qu'avec useSearchParams (qui imposerait un
  // Suspense autour de l'en-tête).
  function onClick(event: React.MouseEvent<HTMLAnchorElement>, locale: Locale) {
    remember(locale);
    event.currentTarget.href = localizedPath(path, locale) + window.location.search + window.location.hash;
  }

  return (
    <nav aria-label={t.header.language} className={className}>
      <ul className="flex items-center rounded-full border border-line p-1 text-xs font-semibold tracking-wider">
        {LOCALES.map((locale) => {
          const active = locale === current;
          return (
            <li key={locale}>
              <a
                href={localizedPath(path, locale)}
                hrefLang={locale}
                lang={locale}
                aria-label={NAMES[locale]}
                aria-current={active ? "true" : undefined}
                onClick={(event) => onClick(event, locale)}
                className={cn("grid min-h-9 min-w-10 place-items-center rounded-full px-2 transition pointer-coarse:min-h-11 pointer-coarse:min-w-11", active ? "bg-ink text-night" : "text-ink/75 hover:text-ink")}
              >
                {locale.toUpperCase()}
              </a>
            </li>
          );
        })}
      </ul>
    </nav>
  );
}
