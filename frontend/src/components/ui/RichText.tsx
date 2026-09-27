import { sanitizeRichText } from "@/lib/sanitize";
import { cn } from "@/lib/utils";

/** Texte riche issu de l'administration, nettoyé côté serveur avant affichage. */
export function RichText({ html, className }: { html: string | null | undefined; className?: string }) {
  const clean = sanitizeRichText(html);
  if (!clean) return null;
  return <div className={cn("rich-text", className)} dangerouslySetInnerHTML={{ __html: clean }} />;
}
