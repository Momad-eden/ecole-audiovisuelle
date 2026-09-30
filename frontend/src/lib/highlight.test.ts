import { expect, test } from "@playwright/test";
import { splitHighlight } from "./highlight";

test("coupe le titre autour du mot en couleur", () => {
  expect(splitHighlight("Faites de votre passion un métier", "un métier")).toEqual(["Faites de votre passion ", "un métier", ""]);
});

test("ignore la casse et garde le texte d'origine", () => {
  expect(splitHighlight("Le studio qui fait sonner Dakar", "SONNER")).toEqual(["Le studio qui fait ", "sonner", " Dakar"]);
});

test("tolère les espaces insécables ajoutés par frenchSpacing", () => {
  expect(splitHighlight("Écoutez !", "Écoutez !")).toEqual(["", "Écoutez !", ""]);
  expect(splitHighlight("Son : lumière", "son : lumière")).toEqual(["", "Son : lumière", ""]);
});

test("seule la première occurrence est colorée", () => {
  expect(splitHighlight("Son, son, son", "son")).toEqual(["", "Son", ", son, son"]);
});

test("renvoie null si le mot est absent ou vide", () => {
  expect(splitHighlight("Titre", "autre")).toBeNull();
  expect(splitHighlight("Titre", "  ")).toBeNull();
  expect(splitHighlight("Titre", null)).toBeNull();
  expect(splitHighlight("Titre", undefined)).toBeNull();
});

test("les caractères spéciaux du mot ne cassent pas la recherche", () => {
  expect(splitHighlight("100 % pratique (vraiment)", "(vraiment)")).toEqual(["100 % pratique ", "(vraiment)", ""]);
});
