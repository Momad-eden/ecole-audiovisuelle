import { formatNumber } from "./i18n/format";
import type { Locale } from "./i18n/locales";

const UNITS: Record<Locale, { byte: string; bytes: string; kilo: string; mega: string }> = {
  fr: { byte: "octet", bytes: "octets", kilo: "Ko", mega: "Mo" },
  en: { byte: "byte", bytes: "bytes", kilo: "KB", mega: "MB" },
};

/** Poids d'un fichier lisible : 820 octets, 12 Ko, 1,2 Mo (en anglais : 820 bytes, 12 KB, 1.2 MB). */
export function fileSize(bytes: number, locale: Locale = "fr"): string {
  const units = UNITS[locale];
  const format = (value: number) => formatNumber(value, locale, { maximumFractionDigits: 1 });
  if (bytes < 1024) return `${bytes} ${bytes > 1 ? units.bytes : units.byte}`;
  if (bytes < 1024 * 1024) return `${format(Math.round(bytes / 1024))} ${units.kilo}`;
  return `${format(Math.round((bytes / (1024 * 1024)) * 10) / 10)} ${units.mega}`;
}

/** « PDF · 1,2 Mo » */
export function fileLabel(extension: string, bytes: number, locale: Locale = "fr"): string {
  return `${extension.toUpperCase()} · ${fileSize(bytes, locale)}`;
}
