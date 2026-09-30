import { expect, test } from "@playwright/test";
import { sitemapEntries } from "./sitemap";

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
