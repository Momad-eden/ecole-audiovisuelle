"use client";

import { en } from "@/lib/i18n/dictionaries/en";
import { LocaleProvider } from "./LocaleProvider";

/** Module à part : seules les pages anglaises téléchargent ce dictionnaire. */
export function EnglishDictionary({ children }: { children: React.ReactNode }) {
  return <LocaleProvider locale="en" t={en}>{children}</LocaleProvider>;
}
