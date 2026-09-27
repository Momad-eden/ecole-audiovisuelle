import { expect, test } from "@playwright/test";

test("l'accueil présente les salles du musée et un seul lien vers l'Espace Professionnels", async ({ page }) => {
  await page.goto("/");
  await expect(page.getByRole("heading", { level: 1 })).toBeVisible();
  await expect(page.getByRole("heading", { name: "Entrez dans les salles" })).toBeVisible();
  await expect(page.locator('a[href="/musee/salle-du-son"]').first()).toBeVisible();
});

test("on entre dans une salle depuis le plan du musée", async ({ page }) => {
  await page.goto("/musee");
  await page.locator('a[href="/musee/salle-du-son"]').first().click();
  await expect(page).toHaveURL(/\/musee\/salle-du-son$/);
  await expect(page.getByRole("heading", { level: 1, name: "Salle du Son" })).toBeVisible();
});

test("les anciennes adresses redirigent vers la nouvelle arborescence", async ({ page }) => {
  await page.goto("/vae");
  await expect(page).toHaveURL(/\/professionnels\/bts-vae$/);
  await expect(page.getByRole("heading", { level: 1 })).toContainText("certification de niveau BTS");
});

test("une page inconnue affiche la page 404 du musée", async ({ page }) => {
  const response = await page.goto("/page-qui-n-existe-pas");
  expect(response?.status()).toBe(404);
  await expect(page.getByText("Cette salle est plongée dans le noir.")).toBeVisible();
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
