"use client";

import { fr } from "@/lib/i18n/dictionaries/fr";
import { LocaleProvider } from "./LocaleProvider";

/** Module à part : seules les pages françaises téléchargent ce dictionnaire. */
export function FrenchDictionary({ children }: { children: React.ReactNode }) {
  return <LocaleProvider locale="fr" t={fr}>{children}</LocaleProvider>;
}
