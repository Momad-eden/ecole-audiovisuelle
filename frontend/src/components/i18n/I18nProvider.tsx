import type { Locale } from "@/lib/i18n/locales";
import { DictionaryProvider } from "./DictionaryProvider";

/**
 * Fournit langue et textes fixes aux composants clients (useLocale, useT).
 * Les dictionnaires contiennent des fonctions (non sérialisables depuis le serveur) : chaque langue a son
 * propre module client, chargé à la demande (DictionaryProvider), pour n'envoyer que celui de la page.
 */
export function I18nProvider({ locale, children }: { locale: Locale; children: React.ReactNode }) {
  return <DictionaryProvider locale={locale}>{children}</DictionaryProvider>;
}
