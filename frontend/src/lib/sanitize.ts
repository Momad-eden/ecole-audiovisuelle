import "server-only";
import sanitizeHtml from "sanitize-html";

/** Nettoie le texte riche saisi dans l'administration : seules des balises de mise en forme sont conservées. */
export function sanitizeRichText(html: string | null | undefined): string {
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
        const external = /^https?:\/\//.test(attribs.href ?? "");
        return { tagName, attribs: external ? { ...attribs, target: "_blank", rel: "noopener noreferrer" } : attribs };
      },
    },
  });
}
