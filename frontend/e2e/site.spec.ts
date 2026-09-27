import { expect, test } from "@playwright/test";

test("l'accueil présente l'école, ses univers et un appel à candidater", async ({ page }) => {
  await page.goto("/");
  await expect(page.getByRole("heading", { level: 1 })).toContainText("Apprenez à faire vibrer");
  await expect(page.getByRole("heading", { name: "Choisissez votre univers" })).toBeVisible();
  await expect(page.locator('a[href="/univers/son"]').first()).toBeAttached();
  await expect(page.getByRole("link", { name: "Candidater" }).first()).toBeVisible();
});

test("on découvre un univers, ses filières et ses métiers", async ({ page }) => {
  await page.goto("/univers");
  // Attendre que le défilement horizontal soit en place (bureau) ; sur mobile, la pile est statique.
  await page.waitForFunction(() => window.matchMedia("(max-width: 1023px)").matches || document.querySelector(".universe-track")?.classList.contains("is-horizontal"));
  // Au clavier : le panneau qui reçoit le focus est amené à l'écran, même pendant le défilement horizontal.
  const panel = page.locator('main a[href="/univers/scene"]').first();
  await panel.focus();
  await expect(panel).toBeInViewport();
  await page.keyboard.press("Enter");
  await expect(page).toHaveURL(/\/univers\/scene$/, { timeout: 15000 });
  await expect(page.getByRole("heading", { level: 1, name: /^Scène\s: régie & lumière$/ })).toBeVisible({ timeout: 15000 });
  await expect(page.getByRole("heading", { name: "Technicien Lumière" })).toBeVisible();
  await expect(page.getByText("Régisseur lumière", { exact: true })).toBeVisible();
});

test("la page Formations range les filières par univers", async ({ page }) => {
  await page.goto("/formations");
  await expect(page.getByRole("heading", { level: 2, name: /^Image\s: vidéo & photo$/ })).toBeVisible();
  await expect(page.getByRole("heading", { name: "Cadrage Sportif et Régie Vidéo" })).toBeVisible();
});

test("les adresses de l'ancien musée redirigent vers les univers et les réalisations", async ({ page }) => {
  await page.goto("/musee/salle-du-son");
  await expect(page).toHaveURL(/\/univers\/son$/);
  await page.goto("/musee");
  await expect(page).toHaveURL(/\/realisations$/);
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
  await page.goto("/professionnels/candidater");
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
  await page.goto("/formations");
  await expect(page.locator("html")).toHaveAttribute("data-theme", "dark");
  if (isMobile) await page.getByRole("button", { name: "Ouvrir le menu" }).click();
  await page.getByRole("button", { name: "Passer au thème clair" }).first().click();
  await expect(page.locator("html")).toHaveAttribute("data-theme", "light");
  await page.reload();
  await expect(page.locator("html")).toHaveAttribute("data-theme", "light");
  if (isMobile) await page.getByRole("button", { name: "Ouvrir le menu" }).click();
  await expect(page.getByRole("button", { name: "Passer au thème sombre" }).first()).toBeVisible();
});
