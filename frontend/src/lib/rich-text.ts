import sanitizeHtml from "sanitize-html";
import { DEFAULT_LOCALE, localizedPath, type Locale } from "./i18n/locales";

/**
 * Nettoie le texte riche saisi dans l'administration : seules des balises de mise en forme sont conservées.
 * Les liens internes (« /emsi/dakar ») sont saisis en français : sur une page anglaise, ils passent sous /en
 * (médias /storage, /api et liens déjà anglais inchangés, voir localizedPath).
 * Module pur, testé sans serveur ; les composants passent par src/lib/sanitize.ts (réservé au serveur).
 */
export function sanitizeRichText(html: string | null | undefined, locale: Locale = DEFAULT_LOCALE): string {
  if (!html) return "";

  return sanitizeHtml(html, {
    allowedTags: ["p", "br", "h2", "h3", "strong", "b", "em", "i", "u", "s", "a", "span", "ul", "ol", "li", "blockquote", "img", "figure", "figcaption"],
    // Mise en forme guidée de l'éditeur : valeurs prises dans des listes fermées, jamais de style libre.
    allowedAttributes: {
      a: ["href", "target", "rel"],
      img: ["src", "alt", "width", "height"],
      span: [
        { name: "data-font", multiple: false, values: ["display", "serif", "mono"] },
        { name: "data-size", multiple: false, values: ["sm", "lg", "xl", "2xl"] },
        { name: "data-color", multiple: false, values: ["brand", "violet", "cyan", "magenta", "gold", "red"] },
      ],
      p: ["style"],
      h2: ["style"],
      h3: ["style"],
    },
    allowedStyles: { "*": { "text-align": [/^(left|center|right|justify)$/] } },
    allowedSchemes: ["http", "https", "mailto", "tel"],
    transformTags: {
      a: (tagName, attribs) => {
        const href = attribs.href ?? "";
        if (/^https?:\/\//.test(href)) return { tagName, attribs: { ...attribs, target: "_blank", rel: "noopener noreferrer" } };
        return { tagName, attribs: href ? { ...attribs, href: localizedPath(href, locale) } : attribs };
      },
    },
  });
}
