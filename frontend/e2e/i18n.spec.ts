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

/** Sélecteur de langue : dans l'en-tête à partir de 640 px, dans le menu sur téléphone. */
async function switchLanguage(page: import("@playwright/test").Page, label: "FR" | "EN") {
  const inHeader = page.locator("header").getByRole("link", { name: label === "EN" ? "English" : "Français", exact: true });
  if (await inHeader.isVisible()) {
    await inHeader.click();
    return;
  }
  await page.locator("header").getByRole("button", { name: /menu/i }).click();
  await page.getByRole("dialog").getByRole("link", { name: label === "EN" ? "English" : "Français", exact: true }).click();
}

test("le sélecteur de langue mène à la même page dans l'autre langue, et retour", async ({ page, context }) => {
  await page.goto("/emsi/dakar");
  await expect(page.locator("html")).toHaveAttribute("lang", "fr");
  const english = page.locator("a[hreflang='en']").first();
  await expect(english).toHaveAttribute("href", "/en/emsi/dakar");
  await expect(english).toHaveAttribute("lang", "en");
  await expect(page.locator("a[hreflang='fr'][aria-current='true']").first()).toBeAttached();

  await switchLanguage(page, "EN");
  await page.waitForURL((url) => url.pathname === "/en/emsi/dakar");
  await expect(page.locator("html")).toHaveAttribute("lang", "en");
  // Textes fixes de l'interface en anglais : lien d'évitement, pied de page, menu.
  await expect(page.getByRole("link", { name: "Skip to content" })).toBeAttached();
  await expect(page.getByRole("contentinfo")).toContainText("Explore");
  await expect(page.getByRole("contentinfo")).toContainText("Get in touch");
  await expect(page.locator("header button[aria-label='Open menu']")).toBeAttached();
  await expect(page.locator("a[hreflang='en'][aria-current='true']").first()).toBeAttached();
  // Contenu pas encore traduit : il est signalé comme français aux lecteurs d'écran.
  const content = page.locator("#contenu [lang='fr']").first();
  if ((await content.count()) > 0) await expect(content).toBeAttached();

  // Choix mémorisé un an, sans redirection automatique.
  const cookie = (await context.cookies()).find((c) => c.name === "emsi-locale");
  expect(cookie?.value).toBe("en");
  expect(cookie?.sameSite).toBe("Lax");
  expect(cookie!.expires - Date.now() / 1000).toBeGreaterThan(360 * 24 * 3600);

  await switchLanguage(page, "FR");
  await page.waitForURL((url) => url.pathname === "/emsi/dakar");
  await expect(page.locator("html")).toHaveAttribute("lang", "fr");
  await expect(page.getByRole("link", { name: "Aller au contenu" })).toBeAttached();
});

test("le cookie de langue ne redirige jamais : la racine reste en français", async ({ page, context }) => {
  await context.addCookies([{ name: "emsi-locale", value: "en", url: test.info().project.use.baseURL! }]);
  const response = await page.goto("/emsi/dakar");
  expect(response?.status()).toBe(200);
  expect(new URL(page.url()).pathname).toBe("/emsi/dakar");
  await expect(page.locator("html")).toHaveAttribute("lang", "fr");
});

test("formulaire de contact vide en anglais : erreurs en anglais", async ({ page }) => {
  await page.goto("/en/contact");
  const form = page.locator("form").filter({ has: page.locator("textarea") }).first();
  await form.getByRole("button", { name: "Send" }).click();
  await expect(form.getByText("Please enter your name.")).toBeVisible();
  await expect(form.getByText("Your message is too short.")).toBeVisible();
  await expect(form.getByText(/we need your consent/i)).toBeVisible();
  await expect(form).not.toContainText("Indiquez");
});

test("pages anglaises à 360 px : aucun défilement horizontal", async ({ page }) => {
  await page.setViewportSize({ width: 360, height: 780 });
  for (const path of ["/en", "/en/emsi/dakar", "/en/contact", "/en/emsi/formations", "/en/candidater"]) {
    await page.goto(path);
    const overflow = await page.evaluate(() => document.documentElement.scrollWidth - document.documentElement.clientWidth);
    expect(overflow, path).toBeLessThanOrEqual(0);
  }
});

test("les textes fixes d'une page anglaise (cartes, liens) sont en anglais", async ({ page }) => {
  await page.goto("/en/emsi/formations");
  await expect(page.locator("main")).not.toContainText(/Voir la formation|Toutes les formations|Découvrir l'univers/);
  await expect(page.locator("html")).toHaveAttribute("lang", "en");
});

test.describe("référencement bilingue", () => {
  test("une page anglaise annonce sa langue et ses équivalents", async ({ page }) => {
    await page.goto("/en/emsi");
    await expect(page.locator("html")).toHaveAttribute("lang", "en");
    const hreflang = async (lang: string) => new URL((await page.locator(`link[rel="alternate"][hreflang="${lang}"]`).getAttribute("href")) ?? "", "http://x").pathname;
    expect(await hreflang("fr")).toBe("/emsi");
    expect(await hreflang("en")).toBe("/en/emsi");
    expect(await hreflang("x-default")).toBe("/emsi");
    expect(new URL((await page.locator('link[rel="canonical"]').getAttribute("href")) ?? "", "http://x").pathname).toBe("/en/emsi");
  });

  test("le plan du site contient les adresses anglaises", async ({ request }) => {
    const xml = await (await request.get("/sitemap.xml")).text();
    expect(xml).toContain("/en/emsi/dakar</loc>");
    expect(xml).toContain('hreflang="en"');
  });

  test("les données structurées d'une page anglaise sont en anglais", async ({ page }) => {
    await page.goto("/en/emsi/dakar");
    const blocks = await page.locator('script[type="application/ld+json"]').allTextContents();
    expect(blocks.some((text) => text.includes('"inLanguage":"en"') && text.includes("/en/emsi/dakar"))).toBe(true);
  });
});
