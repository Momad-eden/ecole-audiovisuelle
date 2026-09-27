import type { Metadata } from "next";
import type { Image, Page } from "./types";

export function pageMetadata(page: Pick<Page, "title" | "seo">, fallbackDescription?: string | null, image?: Image | null): Metadata {
  const description = page.seo?.description || fallbackDescription || undefined;
  // Une description absente n'est pas écrite : la page garde alors celle du site (layout).
  return {
    title: page.seo?.title || page.title,
    ...(description ? { description } : {}),
    openGraph: { title: page.seo?.title || page.title, ...(description ? { description } : {}), images: image ? [{ url: image.url, alt: image.alt }] : undefined },
  };
}
