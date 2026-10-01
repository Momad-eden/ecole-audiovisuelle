import type { Locale } from "@/lib/i18n/locales";
import { sanitizeRichText } from "@/lib/sanitize";
import { cn } from "@/lib/utils";

/** Texte riche issu de l'administration, nettoyé côté serveur avant affichage (liens internes dans la langue de la page). */
export function RichText({ html, locale, className }: { html: string | null | undefined; locale: Locale; className?: string }) {
  const clean = sanitizeRichText(html, locale);
  if (!clean) return null;
  return <div className={cn("rich-text", className)} dangerouslySetInnerHTML={{ __html: clean }} />;
}
