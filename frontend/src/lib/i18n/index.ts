import { en } from "./dictionaries/en";
import { fr, type Dictionary } from "./dictionaries/fr";
import type { Locale } from "./locales";

export type { Dictionary };

const DICTIONARIES: Record<Locale, Dictionary> = { fr, en };

/**
 * Textes fixes d'une langue, pour les composants serveur (pages, layouts, blocs).
 * Côté navigateur, utiliser useT() (src/components/i18n/LocaleProvider) : seul le dictionnaire de la page y est envoyé.
 */
export function getDictionary(locale: Locale): Dictionary {
  return DICTIONARIES[locale];
}
