import type { Metadata } from "next";
import type { Image, Page } from "./types";

export function pageMetadata(page: Pick<Page, "title" | "seo">, fallbackDescription?: string | null, image?: Image | null): Metadata {
  const description = page.seo?.description || fallbackDescription || undefined;
  return {
    title: page.seo?.title || page.title,
    description,
    openGraph: { title: page.seo?.title || page.title, description, images: image ? [{ url: image.url, alt: image.alt }] : undefined },
  };
}
