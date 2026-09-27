import type { Metadata } from "next";
import type { Image, Page } from "./types";

/** Aperçu de partage par défaut (src/app/opengraph-image.tsx). */
export const DEFAULT_SHARE_IMAGE = { url: "/opengraph-image", width: 1200, height: 630, alt: "EMSI — École des Métiers du Son et de l'Image" };

export function pageMetadata(page: Pick<Page, "title" | "seo">, fallbackDescription?: string | null, image?: Image | null): Metadata {
  const description = page.seo?.description || fallbackDescription || undefined;
  // Une description absente n'est pas écrite : la page garde alors celle du site (layout).
  return {
    title: page.seo?.title || page.title,
    ...(description ? { description } : {}),
    openGraph: { title: page.seo?.title || page.title, ...(description ? { description } : {}), images: image ? [{ url: image.url, alt: image.alt }] : [DEFAULT_SHARE_IMAGE] },
  };
}
