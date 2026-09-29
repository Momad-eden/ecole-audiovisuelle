import { expect, test } from "@playwright/test";

test("les anciennes adresses arrivent sur la nouvelle en une seule redirection", async ({ request }) => {
  const redirections: [string, string][] = [
    ["/formations", "/emsi/formations"],
    ["/formations/son-live", "/emsi/formations/son-live"],
    ["/univers", "/emsi"],
    ["/univers/son", "/emsi/univers/son"],
    ["/realisations", "/emsi/realisations"],
    ["/realisations/une-oeuvre", "/emsi/realisations/une-oeuvre"],
    ["/professionnels", "/emsi/professionnels"],
    ["/professionnels/candidater", "/emsi/professionnels/candidater"],
    ["/expositions", "/emsi/realisations"],
    ["/expositions/une-expo", "/emsi/realisations"],
    ["/studio", "/maison-habib-faye/studio"],
    ["/ecole", "/emsi"],
    ["/espace-habib-faye", "/maison-habib-faye"],
    ["/agenda", "/maison-habib-faye/agenda"],
    ["/agenda/un-concert", "/maison-habib-faye/agenda/un-concert"],
    ["/events", "/maison-habib-faye"],
    ["/events/materiel/une-enceinte", "/maison-habib-faye"],
    ["/musee", "/emsi/realisations"],
    ["/musee/oeuvres/une-oeuvre", "/emsi/realisations/une-oeuvre"],
    ["/musee/salle-du-son", "/emsi/univers/son"],
    ["/musee/design", "/emsi/univers/design"],
  ];
  for (const [from, to] of redirections) {
    const response = await request.get(from, { maxRedirects: 0 });
    expect(response.status(), from).toBe(308); // permanent de Next
    expect(new URL(response.headers()["location"], "http://x").pathname, from).toBe(to);
  }
});

// activé après emsi:site-v4 (Task 10)
test.fixme("le menu EMSI s'ouvre au clavier et se referme avec Échap", async ({ page }) => {
  await page.goto("/");
  const bouton = page.getByRole("navigation", { name: "Navigation principale" }).getByRole("button", { name: "EMSI" });
  await bouton.focus();
  await page.keyboard.press("Enter");
  await expect(bouton).toHaveAttribute("aria-expanded", "true");
  await page.keyboard.press("ArrowDown");
  await expect(page.getByRole("link", { name: "L'école" }).first()).toBeFocused();
  await page.keyboard.press("Escape");
  await expect(bouton).toHaveAttribute("aria-expanded", "false");
  await expect(bouton).toBeFocused();
});

// activé après emsi:site-v4 (Task 10)
test.fixme("sur téléphone, le menu EMSI s'ouvre en accordéon", async ({ page }) => {
  await page.setViewportSize({ width: 390, height: 800 });
  await page.goto("/");
  await page.getByRole("button", { name: "Ouvrir le menu" }).click();
  const bouton = page.getByRole("dialog").getByRole("button", { name: /EMSI/ });
  await expect(bouton).toHaveAttribute("aria-expanded", "false");
  await bouton.click();
  await expect(bouton).toHaveAttribute("aria-expanded", "true");
  await expect(page.getByRole("dialog").getByRole("link", { name: "Dakar" })).toBeVisible();
});

// activé après emsi:site-v4 (Task 10)
test.fixme("une page de campus affiche le fil d'Ariane et la sous-navigation EMSI, page courante marquée", async ({ page }) => {
  await page.goto("/emsi/dakar");
  const ariane = page.getByRole("navigation", { name: "Fil d'Ariane" });
  await expect(ariane.getByRole("link", { name: "Accueil" })).toBeVisible();
  await expect(ariane.getByRole("link", { name: "EMSI" })).toBeVisible();
  const sousNav = page.getByRole("navigation", { name: "Rubriques EMSI" });
  await expect(sousNav.getByRole("link", { name: "Dakar" })).toHaveAttribute("aria-current", "page");
});
