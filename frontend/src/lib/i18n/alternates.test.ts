import { expect, test } from "@playwright/test";
import { localeAlternates } from "./alternates";

test("une page française pointe vers elle-même et vers sa version anglaise", () => {
  expect(localeAlternates("/emsi/dakar", "fr")).toEqual({
    canonical: "/emsi/dakar",
    languages: { fr: "/emsi/dakar", en: "/en/emsi/dakar", "x-default": "/emsi/dakar" },
  });
});

test("la version anglaise a sa propre adresse canonique", () => {
  expect(localeAlternates("/emsi/dakar", "en").canonical).toBe("/en/emsi/dakar");
});

test("l'accueil devient /en en anglais", () => {
  expect(localeAlternates("/", "en")).toEqual({ canonical: "/en", languages: { fr: "/", en: "/en", "x-default": "/" } });
});

test("un chemin déjà préfixé par /en est ramené au chemin français", () => {
  expect(localeAlternates("/en/emsi", "en").languages.fr).toBe("/emsi");
});
