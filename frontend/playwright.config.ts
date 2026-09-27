import { defineConfig, devices } from "@playwright/test";

/**
 * Parcours de bout en bout du site public.
 * Prérequis : Laravel (API, base avec le contenu de référence) et `npm run dev` lancés.
 */
export default defineConfig({
  testDir: "./e2e",
  timeout: 60_000,
  use: { baseURL: process.env.E2E_BASE_URL ?? "http://localhost:3000", trace: "retain-on-failure" },
  projects: [
    { name: "bureau", use: { ...devices["Desktop Chrome"] } },
    { name: "mobile", use: { ...devices["Pixel 7"] } },
  ],
});
