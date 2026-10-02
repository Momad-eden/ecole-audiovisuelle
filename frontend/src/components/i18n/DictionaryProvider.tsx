"use client";

import dynamic from "next/dynamic";
import type { Locale } from "@/lib/i18n/locales";

/**
 * Un module (et donc un fichier JS) par langue, chargé à la demande : la page anglaise ne télécharge
 * que le dictionnaire anglais, la page française que le français. Rendus côté serveur, puis préchargés
 * par Next pour l'hydratation (aucun texte manquant à l'affichage).
 */
const French = dynamic(() => import("./FrenchDictionary").then((m) => m.FrenchDictionary));
const English = dynamic(() => import("./EnglishDictionary").then((m) => m.EnglishDictionary));

export function DictionaryProvider({ locale, children }: { locale: Locale; children: React.ReactNode }) {
  return locale === "en" ? <English>{children}</English> : <French>{children}</French>;
}
