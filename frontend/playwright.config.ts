import { defineConfig, devices } from "@playwright/test";

/**
 * Parcours de bout en bout du site public.
 * Prérequis : Laravel (API, base avec le contenu de référence) et `npm run dev` lancés.
 * Le projet « unitaire » teste les fonctions pures de src/ (*.test.ts) sans navigateur ni serveur.
 */
export default defineConfig({
  timeout: 60_000,
  use: { baseURL: process.env.E2E_BASE_URL ?? "http://localhost:3000", trace: "retain-on-failure" },
  projects: [
    { name: "unitaire", testDir: "./src", testMatch: /\.test\.ts$/ },
    // Pages d'essai des héros : créées avant les parcours navigateur, supprimées après.
    { name: "préparation", testDir: "./e2e", testMatch: /showcase\.setup\.ts$/, teardown: "nettoyage" },
    { name: "nettoyage", testDir: "./e2e", testMatch: /showcase\.teardown\.ts$/ },
    { name: "bureau", testDir: "./e2e", dependencies: ["préparation"], use: { ...devices["Desktop Chrome"] } },
    { name: "mobile", testDir: "./e2e", dependencies: ["préparation"], use: { ...devices["Pixel 7"] } },
  ],
});
