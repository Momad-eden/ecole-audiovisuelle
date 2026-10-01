"use client";

import { createContext, useContext } from "react";
import type { Dictionary } from "@/lib/i18n";
import type { Locale } from "@/lib/i18n/locales";

type I18n = { locale: Locale; t: Dictionary };

const I18nContext = createContext<I18n | null>(null);

/**
 * Langue et textes fixes de la page pour les composants clients. Monté par I18nProvider (layout [locale])
 * via FrenchDictionary / EnglishDictionary : le navigateur ne télécharge que le dictionnaire de la page.
 */
export function LocaleProvider({ locale, t, children }: I18n & { children: React.ReactNode }) {
  return <I18nContext.Provider value={{ locale, t }}>{children}</I18nContext.Provider>;
}

function useI18n(): I18n {
  const value = useContext(I18nContext);
  if (!value) throw new Error("useLocale/useT : composant rendu hors du layout [locale] (LocaleProvider absent).");
  return value;
}

export const useLocale = (): Locale => useI18n().locale;

/** Textes fixes de la langue de la page (côté client ; côté serveur : getDictionary(locale)). */
export const useT = (): Dictionary => useI18n().t;
