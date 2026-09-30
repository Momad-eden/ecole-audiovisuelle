import { expect, test } from "@playwright/test";

/**
 * Site bilingue : français à la racine (adresses inchangées), anglais sous /en avec les mêmes chemins.
 * Les liens de changement de langue portent hreflang et sont exclus des vérifications de liens internes.
 */

test("les adresses françaises restent à la racine", async ({ request }) => {
  const response = await request.get("/emsi/dakar");
  expect(response.status()).toBe(200);
});

test("la même page existe en anglais sous /en", async ({ request }) => {
  expect((await request.get("/en/emsi/dakar")).status()).toBe(200);
  expect((await request.get("/en")).status()).toBe(200);
});

test("/fr explicite redirige vers l'adresse sans préfixe, requête conservée", async ({ request }) => {
  const response = await request.get("/fr/emsi", { maxRedirects: 0 });
  expect(response.status()).toBe(308);
  expect(new URL(response.headers()["location"], "http://x").pathname).toBe("/emsi");

  const withQuery = await request.get("/fr/emsi/realisations?univers=son", { maxRedirects: 0 });
  expect(withQuery.status()).toBe(308);
  const location = new URL(withQuery.headers()["location"], "http://x");
  expect(location.pathname + location.search).toBe("/emsi/realisations?univers=son");
});

test("les anciennes adresses redirigent aussi sous /en", async ({ request }) => {
  const response = await request.get("/en/formations/x", { maxRedirects: 0 });
  expect([301, 308]).toContain(response.status());
  expect(new URL(response.headers()["location"], "http://x").pathname).toBe("/en/emsi/formations/x");

  const french = await request.get("/formations/x", { maxRedirects: 0 });
  expect(new URL(french.headers()["location"], "http://x").pathname).toBe("/emsi/formations/x");
});

test("une page anglaise inconnue répond 404", async ({ page }) => {
  const response = await page.goto("/en/xyz-inconnu");
  expect(response?.status()).toBe(404);
});

test("les liens internes d'une page anglaise restent en anglais", async ({ page }) => {
  await page.goto("/en/emsi/dakar");
  const hrefs = await page.locator("a[href^='/']:not([hreflang])").evaluateAll((links) => links.map((a) => a.getAttribute("href") ?? ""));
  const internal = hrefs.filter((href) => !href.startsWith("/storage/") && !href.startsWith("/api/") && !href.startsWith("//"));
  expect(internal.length).toBeGreaterThan(5);
  expect(internal.filter((href) => href !== "/en" && !href.startsWith("/en/") && !href.startsWith("/en?") && !href.startsWith("/en#"))).toEqual([]);
});

test("les liens internes d'une page française restent sans préfixe", async ({ page }) => {
  await page.goto("/emsi/dakar");
  const hrefs = await page.locator("a[href^='/']:not([hreflang])").evaluateAll((links) => links.map((a) => a.getAttribute("href") ?? ""));
  expect(hrefs.length).toBeGreaterThan(5);
  expect(hrefs.filter((href) => href === "/en" || href.startsWith("/en/"))).toEqual([]);
});
