import { delocalizedPath, localizedPath, type Locale } from "./locales";

/**
 * Liens de langue d'une page pour les métadonnées (hreflang) : chaque page existe au même chemin
 * en français (racine) et en anglais (/en) ; le français est la version par défaut (x-default).
 */
export function localeAlternates(path: string, locale: Locale) {
  const fr = delocalizedPath(path);
  const en = localizedPath(fr, "en");
  return { canonical: locale === "en" ? en : fr, languages: { fr, en, "x-default": fr } };
}
