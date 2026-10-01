import "server-only";

/** Nettoyage du texte riche, réservé au serveur : sanitize-html n'est jamais envoyé au navigateur. */
export { sanitizeRichText } from "./rich-text";
