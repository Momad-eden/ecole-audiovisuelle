import type { Locale } from "./locales";

/** Conventions Intl de chaque langue du site : français de France, anglais britannique (comme les traductions DeepL EN-GB). */
const INTL_LOCALE: Record<Locale, string> = { fr: "fr-FR", en: "en-GB" };

export const intlLocale = (locale: Locale) => INTL_LOCALE[locale];

const DEFAULT_DATE: Intl.DateTimeFormatOptions = { day: "numeric", month: "long", year: "numeric" };

/** « 5 octobre 2026 » / « 5 October 2026 », à l'heure de Dakar ; options Intl pour les autres formes (jour, heure…). */
export function formatDate(iso: string | null | undefined, locale: Locale, options: Intl.DateTimeFormatOptions = DEFAULT_DATE): string {
  if (!iso) return "";
  return new Intl.DateTimeFormat(INTL_LOCALE[locale], { timeZone: "Africa/Dakar", ...options }).format(new Date(iso));
}

/**
 * Nombre selon la langue : « 1 250 000 » / « 1,250,000 ».
 * En français, l'espace fine insécable d'Intl est remplacée par une espace ordinaire (format historique du site).
 */
export function formatNumber(value: number, locale: Locale, options?: Intl.NumberFormatOptions): string {
  const text = new Intl.NumberFormat(INTL_LOCALE[locale], options).format(value);
  return locale === "fr" ? text.replace(/ /g, " ") : text;
}

/** Montant toujours en FCFA, sans décimales : « 1 250 000 FCFA » / « 1,250,000 FCFA ». */
export function formatMoney(amount: number, locale: Locale): string {
  return `${formatNumber(amount, locale, { maximumFractionDigits: 0 })} FCFA`;
}
