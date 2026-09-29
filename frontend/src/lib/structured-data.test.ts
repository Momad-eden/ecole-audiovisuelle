import { expect, test } from "@playwright/test";
import { pageStructuredData, jsonLdScript } from "./structured-data";
import type { Block, Page, Place } from "./types";

const page = (over: Partial<Page> = {}): Page => ({ title: "Maison Habib Faye", slug: "maison-habib-faye", type: "free", domain: "maison", seo: null, blocks: [], updatedAt: null, ...over });
const place = (over: Partial<Place> = {}): Place => ({
  id: 3, name: "EMSI Saint-Louis", slug: "saint-louis", kind: "campus", city: "Saint-Louis", address: "Sor, route de l'Université", phone: "+221 33 000 00 00",
  whatsapp: null, email: null, mapUrl: null, openingHours: null, tagline: null, description: null, highlights: [], image: null, ...over,
});
const campusBlock = (campus: unknown): Block => ({ id: "b1", type: "campus_programs", data: { title: "Formations", campus, items: [] } });

test("Maison et Studio : Organization sans parentOrganization", () => {
  for (const domain of ["maison", "studio"] as const) {
    const data = pageStructuredData(page({ domain }), "/maison-habib-faye", [], "https://site.test");
    expect(data).toMatchObject({ "@context": "https://schema.org", "@type": "Organization", name: "Maison Habib Faye", url: "https://site.test/maison-habib-faye" });
    expect(data).not.toHaveProperty("parentOrganization");
  }
});

test("EMSI : EducationalOrganization", () => {
  const data = pageStructuredData(page({ title: "EMSI", domain: "emsi" }), "/emsi", [], "https://site.test");
  expect(data).toMatchObject({ "@type": "EducationalOrganization", name: "EMSI", url: "https://site.test/emsi" });
});

test("page de campus : EducationalOrganization avec adresse postale", () => {
  const blocks = [campusBlock({ id: 3, name: "EMSI Saint-Louis", slug: "saint-louis", city: "Saint-Louis" })];
  const data = pageStructuredData(page({ title: "Saint-Louis", domain: "emsi", blocks }), "/emsi/saint-louis", [place()], "https://site.test");
  expect(data).toMatchObject({
    "@type": "EducationalOrganization",
    name: "EMSI Saint-Louis",
    url: "https://site.test/emsi/saint-louis",
    address: { "@type": "PostalAddress", streetAddress: "Sor, route de l'Université", addressLocality: "Saint-Louis", addressCountry: "SN" },
  });
});

test("campus sans lieu correspondant : adresse limitée à la ville et au pays", () => {
  const blocks = [campusBlock({ id: 9, name: "EMSI Dakar", slug: "dakar", city: "Dakar" })];
  const data = pageStructuredData(page({ domain: "emsi", blocks }), "/emsi/dakar", [place()], "https://site.test");
  expect(data?.address).toEqual({ "@type": "PostalAddress", addressLocality: "Dakar", addressCountry: "SN" });
});

test("page générale : rien de plus que le JSON-LD du site", () => {
  expect(pageStructuredData(page({ domain: "general" }), "/contact", [], "https://site.test")).toBeNull();
});

test("le script échappe les balises", () => {
  expect(jsonLdScript({ name: "</script><b>" })).not.toContain("<");
});
