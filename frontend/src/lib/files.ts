/** Poids d'un fichier lisible en français : 820 octets, 12 Ko, 1,2 Mo. */
export function fileSize(bytes: number): string {
  const format = (value: number) => new Intl.NumberFormat("fr-FR", { maximumFractionDigits: 1 }).format(value);
  if (bytes < 1024) return `${bytes} octet${bytes > 1 ? "s" : ""}`;
  if (bytes < 1024 * 1024) return `${format(Math.round(bytes / 1024))} Ko`;
  return `${format(Math.round((bytes / (1024 * 1024)) * 10) / 10)} Mo`;
}

/** « PDF · 1,2 Mo » */
export function fileLabel(extension: string, bytes: number): string {
  return `${extension.toUpperCase()} · ${fileSize(bytes)}`;
}
