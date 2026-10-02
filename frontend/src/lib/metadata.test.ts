import { expect, test } from "@playwright/test";
import { pageMetadata } from "./metadata";

const page = { title: "L'école", seo: null };

test("une page anglaise garde l'aperçu de partage anglais et sa langue", () => {
  const og = pageMetadata(page, "en").openGraph as Record<string, unknown>;
  expect(og.locale).toBe("en_GB");
  expect(og.type).toBe("website");
  expect(og.images).toEqual([expect.objectContaining({ url: "/en/opengraph-image", alt: "EMSI — School of Sound and Image Professions" })]);
});

test("une page française garde l'aperçu français", () => {
  const og = pageMetadata(page, "fr").openGraph as Record<string, unknown>;
  expect(og.locale).toBe("fr_SN");
  expect(og.images).toEqual([expect.objectContaining({ url: "/opengraph-image" })]);
});

test("l'image de la page reste prioritaire", () => {
  const og = pageMetadata(page, "en", null, { url: "/storage/a.jpg", alt: "A" }).openGraph as Record<string, unknown>;
  expect(og.images).toEqual([{ url: "/storage/a.jpg", alt: "A" }]);
});
