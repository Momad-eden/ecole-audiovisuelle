/** Langues du site : le français à la racine (adresses historiques), l'anglais sous /en avec les mêmes chemins. */
export const LOCALES = ["fr", "en"] as const;
export type Locale = (typeof LOCALES)[number];
export const DEFAULT_LOCALE: Locale = "fr";

export function isLocale(value: unknown): value is Locale {
  return typeof value === "string" && (LOCALES as readonly string[]).includes(value);
}

/** Langue d'un paramètre de route déjà validé par le layout [locale] (les layouts imbriqués le reçoivent typé string). */
export function asLocale(value: string): Locale {
  return isLocale(value) ? value : DEFAULT_LOCALE;
}

/** Préfixe de langue en tête d'un chemin (« /en », « /en/… », « /en?… », « /en#… »), sinon null. */
function prefixOf(path: string): Locale | null {
  const match = path.match(/^\/([a-z]{2})(?=$|[/?#])/);
  return match && isLocale(match[1]) ? match[1] : null;
}

/** Chemins jamais préfixés : médias et API servis tels quels. */
const UNLOCALIZED = /^\/(storage|api|_next)(?=$|[/?#])/;

/**
 * Adresse d'un lien interne dans une langue : inchangée en français, préfixée par /en en anglais
 * (« / » → « /en »). Les liens externes, ancres seules, mailto:, tel:, /storage… sont rendus tels quels.
 * Les adresses venues de l'API sont françaises (/emsi/dakar) : c'est ici qu'elles passent en anglais.
 */
export function localizedPath(path: string, locale: Locale): string {
  if (locale === DEFAULT_LOCALE) return path;
  // Seuls les chemins absolus du site (« /… », pas « //hôte ») sont concernés.
  if (!path.startsWith("/") || path.startsWith("//") || UNLOCALIZED.test(path) || prefixOf(path)) return path;
  return path === "/" || /^\/[?#]/.test(path) ? `/${locale}${path.slice(1)}` : `/${locale}${path}`;
}

/** Langue d'un chemin visible dans le navigateur (« /en/… » → en, sinon fr). */
export function localeFromPath(pathname: string): Locale {
  return prefixOf(pathname) ?? DEFAULT_LOCALE;
}

/** Chemin sans préfixe de langue (« /en/emsi » → « /emsi », « /en » → « / ») : c'est la forme des adresses du menu. */
export function delocalizedPath(pathname: string): string {
  const prefix = prefixOf(pathname);
  if (!prefix) return pathname;
  const rest = pathname.slice(prefix.length + 1);
  return rest.startsWith("/") ? rest : `/${rest}`;
}
