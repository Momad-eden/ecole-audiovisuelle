import { expect, test } from "@playwright/test";
import { asLocale, delocalizedPath, isLocale, localeFromPath, localizedPath, LOCALES } from "./locales";

test("les langues du site : français puis anglais", () => {
  expect(LOCALES).toEqual(["fr", "en"]);
  expect(isLocale("fr")).toBe(true);
  expect(isLocale("en")).toBe(true);
  expect(isLocale("de")).toBe(false);
  expect(isLocale(undefined)).toBe(false);
  expect(asLocale("en")).toBe("en");
  expect(asLocale("de")).toBe("fr");
});

test("en français, les adresses restent inchangées", () => {
  expect(localizedPath("/", "fr")).toBe("/");
  expect(localizedPath("/emsi/dakar", "fr")).toBe("/emsi/dakar");
  expect(localizedPath("/candidater?campus=emsi-dakar", "fr")).toBe("/candidater?campus=emsi-dakar");
});

test("en anglais, les adresses internes passent sous /en", () => {
  expect(localizedPath("/", "en")).toBe("/en");
  expect(localizedPath("/emsi/dakar", "en")).toBe("/en/emsi/dakar");
  expect(localizedPath("/candidater?campus=emsi-dakar", "en")).toBe("/en/candidater?campus=emsi-dakar");
  expect(localizedPath("/?page=2", "en")).toBe("/en?page=2");
  expect(localizedPath("/#univers", "en")).toBe("/en#univers");
  expect(localizedPath("/emsi#univers", "en")).toBe("/en/emsi#univers");
  // Déjà préfixée : pas de double préfixe.
  expect(localizedPath("/en/emsi", "en")).toBe("/en/emsi");
  expect(localizedPath("/en", "en")).toBe("/en");
  // Une page dont l'adresse commence par « en » n'est pas confondue avec le préfixe.
  expect(localizedPath("/entreprises", "en")).toBe("/en/entreprises");
});

test("les liens externes, ancres, mails, téléphones et médias ne sont jamais préfixés", () => {
  for (const href of ["https://wa.me/221", "http://example.com/x", "//cdn.example.com/a.js", "#filieres", "?page=2", "mailto:a@b.sn", "tel:+221", "/storage/a.pdf", "/api/v1/public/site", "relatif", ""]) {
    expect(localizedPath(href, "en")).toBe(href);
  }
});

test("retrouver la langue et l'adresse française d'un chemin", () => {
  expect(localeFromPath("/en/emsi")).toBe("en");
  expect(localeFromPath("/en")).toBe("en");
  expect(localeFromPath("/emsi")).toBe("fr");
  expect(localeFromPath("/entreprises")).toBe("fr");
  expect(delocalizedPath("/en/emsi/dakar")).toBe("/emsi/dakar");
  expect(delocalizedPath("/en")).toBe("/");
  expect(delocalizedPath("/fr/emsi")).toBe("/emsi");
  expect(delocalizedPath("/emsi")).toBe("/emsi");
  expect(delocalizedPath("/entreprises")).toBe("/entreprises");
});
