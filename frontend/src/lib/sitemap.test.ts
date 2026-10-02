import { expect, test } from "@playwright/test";
import { bilingualSitemapEntries, sitemapEntries } from "./sitemap";

test("une adresse n'apparaît qu'une fois, avec la date de la page gérée dans l'admin", () => {
  const entries = sitemapEntries(
    ["/emsi", "/actualites"],
    [{ path: "/emsi", updatedAt: "2026-09-29T10:00:00+00:00" }, { path: "/", updatedAt: null }],
    "https://emsi.sn",
  );

  expect(entries).toEqual([
    { url: "https://emsi.sn/emsi", lastModified: "2026-09-29T10:00:00+00:00" },
    { url: "https://emsi.sn/actualites" },
    { url: "https://emsi.sn/", lastModified: undefined },
  ]);
});

test("le plan du site liste chaque adresse en français et en anglais, avec leurs équivalents", () => {
  const entries = bilingualSitemapEntries(["/emsi"], [{ path: "/", updatedAt: "2026-09-30T10:00:00+00:00" }], "https://emsi.sn");

  expect(entries).toEqual([
    { url: "https://emsi.sn/emsi", alternates: { languages: { fr: "https://emsi.sn/emsi", en: "https://emsi.sn/en/emsi" } } },
    { url: "https://emsi.sn/en/emsi", alternates: { languages: { fr: "https://emsi.sn/emsi", en: "https://emsi.sn/en/emsi" } } },
    { url: "https://emsi.sn/", lastModified: "2026-09-30T10:00:00+00:00", alternates: { languages: { fr: "https://emsi.sn/", en: "https://emsi.sn/en" } } },
    { url: "https://emsi.sn/en", lastModified: "2026-09-30T10:00:00+00:00", alternates: { languages: { fr: "https://emsi.sn/", en: "https://emsi.sn/en" } } },
  ]);
});
