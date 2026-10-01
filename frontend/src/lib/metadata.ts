import type { Metadata } from "next";
import { getDictionary } from "./i18n";
import { localizedPath, type Locale } from "./i18n/locales";
import type { Image, Page } from "./types";

/** Aperçu de partage par défaut (src/app/[locale]/opengraph-image.tsx). */
export const DEFAULT_SHARE_IMAGE = { url: "/opengraph-image", width: 1200, height: 630, alt: "EMSI — École des Métiers du Son et de l'Image" };

/** Aperçu de partage par défaut dans la langue de la page (image, texte alternatif). */
export function defaultShareImage(locale: Locale) {
  return { ...DEFAULT_SHARE_IMAGE, url: localizedPath(DEFAULT_SHARE_IMAGE.url, locale), alt: getDictionary(locale).meta.shareImageAlt };
}

/**
 * Bases Open Graph d'une page : Next remplace l'openGraph du layout au lieu de le fusionner, d'où type,
 * langue et image par défaut repris ici pour chaque page.
 */
export function openGraphBase(locale: Locale) {
  return { type: "website" as const, locale: getDictionary(locale).meta.ogLocale, images: [defaultShareImage(locale)] };
}

export function pageMetadata(page: Pick<Page, "title" | "seo">, locale: Locale, fallbackDescription?: string | null, image?: Image | null): Metadata {
  const description = page.seo?.description || fallbackDescription || undefined;
  const title = page.seo?.title || page.title;
  // Une description absente n'est pas écrite : la page garde alors celle du site (layout).
  return {
    title,
    ...(description ? { description } : {}),
    openGraph: { ...openGraphBase(locale), title, ...(description ? { description } : {}), ...(image ? { images: [{ url: image.url, alt: image.alt }] } : {}) },
  };
}
