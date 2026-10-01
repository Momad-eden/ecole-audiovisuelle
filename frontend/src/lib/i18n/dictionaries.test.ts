import { expect, test } from "@playwright/test";
import { en } from "./dictionaries/en";
import { fr } from "./dictionaries/fr";
import { getDictionary } from "./index";

type Tree = Record<string, unknown>;

/** Parcourt les deux dictionnaires en parallèle : chemin, valeur française, valeur anglaise. */
function* pairs(a: unknown, b: unknown, path = ""): Generator<[string, unknown, unknown]> {
  yield [path, a, b];
  if (a && typeof a === "object" && !Array.isArray(a)) {
    const keys = new Set([...Object.keys(a as Tree), ...Object.keys((b ?? {}) as Tree)]);
    for (const key of keys) yield* pairs((a as Tree)[key], ((b ?? {}) as Tree)[key], path ? `${path}.${key}` : key);
  }
}

const sample = (arity: number) => Array.from({ length: arity }, (_, i) => (i === 0 ? "Saint-Louis" : i + 1));

test("le français et l'anglais ont exactement les mêmes clés et les mêmes formes", () => {
  const problems: string[] = [];
  for (const [path, a, b] of pairs(fr, en)) {
    if (typeof a !== typeof b) problems.push(`${path} : ${typeof a} / ${typeof b}`);
    else if (Array.isArray(a) !== Array.isArray(b)) problems.push(`${path} : liste d'un seul côté`);
    else if (Array.isArray(a) && a.length !== (b as unknown[]).length) problems.push(`${path} : ${a.length} / ${(b as unknown[]).length} éléments`);
    else if (typeof a === "function" && a.length !== (b as (...args: unknown[]) => unknown).length) problems.push(`${path} : ${a.length} / ${(b as () => unknown).length} paramètres`);
  }
  expect(problems).toEqual([]);
});

test("aucun texte vide, dans aucune langue (fonctions comprises)", () => {
  const empty: string[] = [];
  for (const [name, dictionary] of [["fr", fr], ["en", en]] as const) {
    for (const [path, value] of pairs(dictionary, dictionary)) {
      const texts = typeof value === "string" ? [value] : Array.isArray(value) ? value : typeof value === "function" ? [(value as (...args: unknown[]) => unknown)(...sample(value.length))] : [];
      for (const text of texts) if (typeof text !== "string" || text.trim() === "") empty.push(`${name}.${path}`);
    }
  }
  expect(empty).toEqual([]);
});

test("getDictionary rend le dictionnaire de la langue demandée", () => {
  expect(getDictionary("fr")).toBe(fr);
  expect(getDictionary("en")).toBe(en);
  expect(fr.header.skipToContent).toBe("Aller au contenu");
  expect(en.header.skipToContent).toBe("Skip to content");
  expect(en.hero.listen).toBe("Listen to the work");
});
