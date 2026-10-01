import type { Page, Place } from "./types";
import { localizedPath, type Locale } from "./i18n/locales";

type JsonLd = Record<string, unknown>;

/** Sérialise le JSON-LD pour une balise <script> : « < » est échappé pour qu'aucune donnée ne referme la balise. */
export function jsonLdScript(data: JsonLd): string {
  return JSON.stringify(data).replace(/</g, "\\u003c");
}

/**
 * Données structurées d'une page à blocs selon son domaine.
 * Centre culturel / Studio : Organization ; EMSI : EducationalOrganization ; page de campus (bloc
 * « campus_programs ») : EducationalOrganization avec l'adresse du campus. Domaine général : rien
 * de plus que le JSON-LD du site (layout.tsx).
 */
export function pageStructuredData(page: Pick<Page, "title" | "domain" | "blocks">, path: string, places: Place[], origin: string, locale: Locale = "fr"): JsonLd | null {
  const url = `${origin}${localizedPath(path, locale)}`;
  const inLanguage = locale;
  const campusBlock = page.blocks.find((block) => block.type === "campus_programs");
  const campus = campusBlock?.data.campus as { id: number; name: string; city: string | null } | null | undefined;

  if (campus) {
    const place = places.find((p) => p.id === campus.id);
    const city = place?.city ?? campus.city;
    const address: JsonLd = { "@type": "PostalAddress" };
    if (place?.address) address.streetAddress = place.address;
    if (city) address.addressLocality = city;
    address.addressCountry = "SN";
    return { "@context": "https://schema.org", "@type": "EducationalOrganization", name: campus.name, url, inLanguage, address, ...(place?.phone ? { telephone: place.phone } : {}) };
  }
  if (page.domain === "emsi") return { "@context": "https://schema.org", "@type": "EducationalOrganization", name: page.title, url, inLanguage };
  if (page.domain === "maison" || page.domain === "studio") return { "@context": "https://schema.org", "@type": "Organization", name: page.title, url, inLanguage };
  return null;
}
