import { expect, test } from "@playwright/test";
import { domainSection } from "./domains";
import type { Site } from "./types";

const menus: Site["menus"]["main"] = [
  { label: "Accueil", url: "/", isButton: false, children: [] },
  {
    label: "EMSI",
    url: "/emsi",
    isButton: false,
    children: [
      { label: "Dakar", url: "/emsi/dakar" },
      { label: "Formations", url: "/emsi/formations" },
    ],
  },
  { label: "Maison", url: "/maison-habib-faye", isButton: false, children: [{ label: "Agenda", url: "/maison-habib-faye/agenda" }] },
  { label: "Contact", url: "/contact", isButton: true, children: [] },
];

test("retrouve la section d'une sous-page", () => {
  expect(domainSection(menus, "/emsi/dakar")?.parent.label).toBe("EMSI");
});

test("le plus long préfixe l'emporte", () => {
  expect(domainSection(menus, "/emsi/formations/technicien-lumiere")?.current?.url).toBe("/emsi/formations");
});

test("la page d'entrée d'une section n'a pas de page courante", () => {
  const section = domainSection(menus, "/emsi");
  expect(section?.parent.label).toBe("EMSI");
  expect(section?.current).toBeUndefined();
});

test("null hors des sections", () => {
  expect(domainSection(menus, "/contact")).toBeNull();
  expect(domainSection(menus, "/")).toBeNull();
  expect(domainSection(menus, "/emsiliens")).toBeNull();
});
