import { expect, test } from "@playwright/test";
import { plainTitle, titleParts, titleWords } from "./emphasis";

test("les mots entre astérisques sont mis en valeur", () => {
  expect(titleParts("l'art comme *métier*")).toEqual([{ text: "l'art comme ", accent: false }, { text: "métier", accent: true }]);
  expect(titleParts("*Sur scène*, au studio")).toEqual([{ text: "Sur scène", accent: true }, { text: ", au studio", accent: false }]);
});

test("un astérisque seul reste tel quel, un titre vide ne donne rien", () => {
  expect(titleParts("Note * importante")).toEqual([{ text: "Note * importante", accent: false }]);
  expect(titleParts(null)).toEqual([]);
});

test("le titre sans astérisques sert aux étiquettes accessibles", () => {
  expect(plainTitle("La culture comme *héritage*, l'art comme *métier*")).toBe("La culture comme héritage, l'art comme métier");
});

test("les mots gardent leur mise en valeur pour le dévoilement mot à mot", () => {
  expect(titleWords("Au studio, *en tournage*")).toEqual([
    [{ text: "Au", accent: false }], [{ text: "studio,", accent: false }], [{ text: "en", accent: true }], [{ text: "tournage", accent: true }],
  ]);
});

test("la ponctuation collée à un mot mis en valeur reste dans le même mot", () => {
  expect(titleWords("comme *héritage*, l'art")).toEqual([
    [{ text: "comme", accent: false }], [{ text: "héritage", accent: true }, { text: ",", accent: false }], [{ text: "l'art", accent: false }],
  ]);
});
