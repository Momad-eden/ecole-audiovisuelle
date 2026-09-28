import { expect, test } from "@playwright/test";
import { accentVars, readableInk } from "./contrast";

const DARK = "#07070a";
const WHITE = "#ffffff";

test("un fond sombre choisi dans l'admin reçoit un texte blanc", () => {
  expect(readableInk("#450b45")).toBe(WHITE);
  expect(readableInk("#1e3a8a")).toBe(WHITE);
});

test("un fond clair ou vif reçoit un texte presque noir", () => {
  expect(readableInk("#ff7a1a")).toBe(DARK);
  expect(readableInk("#fde047")).toBe(DARK);
  expect(readableInk("#8b6cff")).toBe(DARK);
});

test("les écritures courtes et rgb() sont comprises", () => {
  expect(readableInk("#fff")).toBe(DARK);
  expect(readableInk("rgb(69, 11, 69)")).toBe(WHITE);
});

test("une couleur illisible ne donne rien : la couleur par défaut du thème s'applique", () => {
  expect(readableInk("var(--color-brand)")).toBe(null);
  expect(readableInk("")).toBe(null);
});

test("en thème clair, le fond est assombri : le texte y est calculé sur la couleur assombrie", () => {
  expect(readableInk("#ff7a1a", { darken: true })).toBe(WHITE);
  expect(readableInk("#fde047", { darken: true })).toBe(WHITE);
});

test("une couleur de titre choisie dans l'admin fournit aussi la couleur du texte posé dessus", () => {
  expect(accentVars("#450b45")).toEqual({ "--accent": "#450b45", "--accent-on": WHITE, "--accent-on-light": WHITE });
  expect(accentVars("#ff7a1a")).toEqual({ "--accent": "#ff7a1a", "--accent-on": DARK, "--accent-on-light": WHITE });
  expect(accentVars("var(--color-gold)")).toEqual({ "--accent": "var(--color-gold)" });
  expect(accentVars(null)).toBeUndefined();
});
