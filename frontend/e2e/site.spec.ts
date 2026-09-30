import { expect, test } from "@playwright/test";

test("l'accueil présente les trois domaines et un appel à candidater", async ({ page }) => {
  await page.goto("/");
  await expect(page.getByRole("heading", { level: 1 })).toContainText("La culture comme héritage");
  const triptyque = page.getByTestId("domains-block");
  await expect(triptyque.locator('a[href="/maison-habib-faye"]').first()).toBeAttached();
  await expect(triptyque.locator('a[href="/emsi"]')).toBeAttached();
  await expect(triptyque.locator('a[href="/maison-habib-faye/studio"]')).toBeAttached();
  // Sur téléphone, le lien « Candidater » vit dans le menu replié : il est présent dans la page.
  await expect(page.locator('a[href^="/candidater"]').first()).toBeAttached();
});

test("la page du campus de Dakar présente le Grand Théâtre comme partenaire et ses formations", async ({ page }) => {
  await page.goto("/emsi/dakar");
  await expect(page.getByRole("heading", { level: 1, name: "Campus de Dakar" })).toBeVisible();
  await expect(page.getByRole("heading", { name: "Les formations à Dakar" })).toBeVisible();
  await expect(page.getByRole("heading", { name: "Notre partenaire, le Grand Théâtre National" })).toBeVisible();
});

test("on découvre un univers, ses filières et ses métiers", async ({ page }) => {
  await page.goto("/");
  await page.locator('footer a[href="/emsi/univers/scene"]').first().click();
  await expect(page).toHaveURL(/\/emsi\/univers\/scene$/, { timeout: 15000 });
  await expect(page.getByRole("heading", { level: 1, name: /^Scène\s: régie & lumière$/ })).toBeVisible({ timeout: 15000 });
  await expect(page.getByRole("heading", { name: "Technicien Lumière" })).toBeVisible();
  await expect(page.getByText("Régisseur lumière", { exact: true })).toBeVisible();
});

test("la page Formations range les filières par univers", async ({ page }) => {
  await page.goto("/emsi/formations");
  await expect(page.getByRole("heading", { level: 2, name: /^Image\s: vidéo & photo$/ })).toBeVisible();
  await expect(page.getByRole("heading", { name: "Cadrage Sportif et Régie Vidéo" })).toBeVisible();
});

test("les adresses de l'ancien musée redirigent vers les univers et les réalisations", async ({ page }) => {
  await page.goto("/musee/salle-du-son");
  await expect(page).toHaveURL(/\/univers\/son$/);
  await page.goto("/musee");
  await expect(page).toHaveURL(/\/emsi\/realisations$/);
  await expect(page.getByRole("heading", { level: 1, name: "Faites par nos étudiants" })).toBeVisible();
});

test("les anciennes adresses redirigent vers la nouvelle arborescence", async ({ page }) => {
  await page.goto("/vae");
  await expect(page).toHaveURL(/\/professionnels\/bts-vae$/);
  await expect(page.getByRole("heading", { level: 1 })).toContainText("certification de niveau BTS");
});

test("une page inconnue affiche la page 404 du musée", async ({ page }) => {
  const response = await page.goto("/page-qui-n-existe-pas");
  expect(response?.status()).toBe(404);
  await expect(page.getByText("Noir complet sur le plateau.")).toBeVisible();
});

test("le formulaire de contact signale les champs manquants en français", async ({ page }) => {
  await page.goto("/contact");
  await page.getByRole("button", { name: "Envoyer" }).click();
  await expect(page.getByText("Indiquez votre nom.")).toBeVisible();
  await expect(page.getByText("Votre accord est nécessaire pour que nous puissions vous répondre.")).toBeVisible();
});

test("la candidature professionnelle guide le candidat étape par étape", async ({ page }) => {
  await page.goto("/emsi/professionnels/candidater");
  const closed = page.getByText("Aucune candidature n'est ouverte pour le moment.");
  if (await closed.isVisible()) {
    test.skip(true, "Aucune session professionnelle ouverte dans cette base.");
  }

  await page.locator('input[name="offeringId"]').first().check();
  await page.getByRole("button", { name: "Continuer" }).click();
  await page.getByRole("textbox", { name: "Prénom", exact: true }).fill("Awa");
  await page.getByRole("textbox", { name: "Nom", exact: true }).fill("Ndiaye");
  await page.getByRole("button", { name: "Continuer" }).click();
  await page.getByRole("textbox", { name: "Téléphone", exact: true }).fill("abc");
  await page.getByRole("button", { name: "Continuer" }).click();
  await expect(page.getByText("Numéro invalide (ex. +221 77 123 45 67).")).toBeVisible();
});

test("le changeur de thème passe en clair et s'en souvient", async ({ page, isMobile }) => {
  await page.goto("/emsi/formations");
  await expect(page.locator("html")).toHaveAttribute("data-theme", "dark");
  if (isMobile) await page.getByRole("button", { name: "Ouvrir le menu" }).click();
  await page.getByRole("button", { name: "Passer au thème clair" }).first().click();
  await expect(page.locator("html")).toHaveAttribute("data-theme", "light");
  await page.reload();
  await expect(page.locator("html")).toHaveAttribute("data-theme", "light");
  if (isMobile) await page.getByRole("button", { name: "Ouvrir le menu" }).click();
  await expect(page.getByRole("button", { name: "Passer au thème sombre" }).first()).toBeVisible();
});

test("le studio présente ses services, ses productions et la réservation d'une session", async ({ page }) => {
  await page.goto("/maison-habib-faye/studio");
  await expect(page.getByRole("heading", { level: 1 })).toContainText("Ici, votre son");
  await expect(page.getByRole("heading", { name: "Du premier enregistrement au master" })).toBeVisible();
  await expect(page.getByRole("heading", { name: "Mixage" })).toBeVisible();
  await expect(page.getByRole("heading", { name: "Réserver une session" })).toBeVisible();
  await expect(page.getByLabel("Votre demande")).toHaveValue("studio_session", { timeout: 15000 });
});

test("la Maison Habib Faye et sa programmation sont accessibles depuis le pied de page", async ({ page }) => {
  await page.goto("/");
  await page.locator('footer a[href="/maison-habib-faye"]').click();
  await expect(page.getByRole("heading", { level: 1, name: "Espace Habib Faye" })).toBeVisible({ timeout: 15000 });
  await page.goto("/maison-habib-faye/agenda");
  await expect(page.getByRole("heading", { level: 1, name: "Programmation" })).toBeVisible();
});

test("la page EMSI présente les deux campus et oriente vers la candidature", async ({ page }) => {
  await page.goto("/emsi");
  await expect(page.getByRole("heading", { level: 1 })).toContainText("Deux écoles");
  await expect(page.getByRole("link", { name: "Candidater à Dakar" })).toHaveAttribute("href", "/candidater?campus=emsi-dakar");
  await expect(page.getByRole("link", { name: "Candidater à Saint-Louis" })).toHaveAttribute("href", "/candidater?campus=emsi-saint-louis");
  // Chaque carte mène aussi à la page de son campus.
  const pages = page.getByRole("link", { name: "Découvrir le campus" });
  await expect(pages).toHaveCount(2);
  expect(await pages.evaluateAll((links) => links.map((link) => link.getAttribute("href")).sort())).toEqual(["/emsi/dakar", "/emsi/saint-louis"]);
});

// Contenu ajouté par `emsi:site-v4` (page EMSI complétée) : à relancer après la mise à niveau de la base.
test("la page EMSI présente les univers ; /univers y mène", async ({ page }) => {
  await page.goto("/univers");
  await expect(page).toHaveURL(/\/emsi#univers$/, { timeout: 15000 });
  await expect(page.locator("#univers")).toBeAttached();
  await expect(page.locator('a[href^="/emsi/univers/"]').first()).toBeAttached();
});

test("les liens partagés affichent l'aperçu de l'EMSI", async ({ page, request }) => {
  await page.goto("/maison-habib-faye/studio");
  const image = await page.locator('meta[property="og:image"]').first().getAttribute("content");
  expect(image).toContain("/opengraph-image");
  const response = await request.get("/opengraph-image");
  expect(response.status()).toBe(200);
  expect(response.headers()["content-type"]).toContain("image/png");
});
