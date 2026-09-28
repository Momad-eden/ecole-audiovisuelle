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
    { name: "bureau", testDir: "./e2e", use: { ...devices["Desktop Chrome"] } },
    { name: "mobile", testDir: "./e2e", use: { ...devices["Pixel 7"] } },
  ],
});
