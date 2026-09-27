import "server-only";
import sanitizeHtml from "sanitize-html";

/** Nettoie le texte riche saisi dans l'administration : seules des balises de mise en forme sont conservées. */
export function sanitizeRichText(html: string | null | undefined): string {
  if (!html) return "";

  return sanitizeHtml(html, {
    allowedTags: ["p", "br", "h2", "h3", "strong", "b", "em", "i", "u", "a", "ul", "ol", "li", "blockquote", "img", "figure", "figcaption"],
    allowedAttributes: { a: ["href", "target", "rel"], img: ["src", "alt", "width", "height"] },
    allowedSchemes: ["http", "https", "mailto", "tel"],
    transformTags: {
      a: (tagName, attribs) => {
        const external = /^https?:\/\//.test(attribs.href ?? "");
        return { tagName, attribs: external ? { ...attribs, target: "_blank", rel: "noopener noreferrer" } : attribs };
      },
    },
  });
}
