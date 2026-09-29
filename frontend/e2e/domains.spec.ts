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

// Pages d'essai créées par `php artisan emsi:domains-showcase` (showcase.setup.ts).
test.describe("nouveaux blocs", () => {
  const MAISONS = [
    ["Maison Habib Faye", "/maison-habib-faye"],
    ["EMSI", "/emsi"],
    ["Impact Live Studio", "/maison-habib-faye/studio"],
  ] as const;

  test("le triptyque « Nos trois maisons » : trois panneaux cliquables, côte à côte sur ordinateur", async ({ page, isMobile }) => {
    test.skip(isMobile, "Disposition ordinateur.");
    await page.setViewportSize({ width: 1440, height: 900 });
    await page.goto("/essai-domaines-accueil");
    const triptyque = page.getByTestId("domains-block");
    await expect(triptyque.getByRole("heading", { level: 1, name: /La culture comme héritage/ })).toBeVisible();
    const boxes = [];
    for (const [name, href] of MAISONS) {
      const lien = triptyque.getByRole("link", { name, exact: true });
      await expect(lien).toHaveAttribute("href", href);
      await expect(triptyque.getByRole("heading", { level: 2, name, exact: true })).toBeVisible();
      boxes.push((await triptyque.getByTestId("domain-panel").nth(boxes.length).boundingBox())!);
    }
    expect(boxes[1].x).toBeGreaterThan(boxes[0].x);
    expect(boxes[2].x).toBeGreaterThan(boxes[1].x);
    expect(Math.abs(boxes[0].y - boxes[2].y)).toBeLessThan(2);
    expect(boxes[0].height).toBeGreaterThan(500);

    // Au survol de la souris, le panneau survolé s'élargit.
    await page.mouse.move(boxes[0].x + boxes[0].width / 2, boxes[0].y + 200);
    await expect.poll(async () => (await triptyque.getByTestId("domain-panel").first().boundingBox())!.width).toBeGreaterThan(boxes[0].width * 1.2);

    await triptyque.getByRole("link", { name: "EMSI", exact: true }).click();
    await expect(page).toHaveURL(/\/emsi$/);
  });

  test("le triptyque s'empile sur téléphone, sans défilement horizontal", async ({ page }) => {
    await page.setViewportSize({ width: 390, height: 844 });
    await page.goto("/essai-domaines-accueil");
    const panneaux = page.getByTestId("domain-panel");
    await expect(panneaux).toHaveCount(3);
    const [a, b, c] = await Promise.all([0, 1, 2].map(async (i) => (await panneaux.nth(i).boundingBox())!));
    expect(Math.abs(a.x - b.x)).toBeLessThan(2);
    expect(b.y).toBeGreaterThanOrEqual(a.y + a.height - 1);
    expect(c.y).toBeGreaterThanOrEqual(b.y + b.height - 1);
    for (const [name] of MAISONS) await expect(page.getByRole("link", { name, exact: true })).toBeVisible();
    expect(await page.evaluate(() => document.documentElement.scrollWidth)).toBeLessThanOrEqual(390);
  });

  test("« Nous soutenir » : le formulaire est envoyé puis remplacé par un message de confirmation", async ({ page }) => {
    // Aucun message réel n'est enregistré : la réponse de l'API est simulée.
    let envoi: Record<string, unknown> | null = null;
    await page.route("**/api/v1/public/support", async (route) => {
      envoi = route.request().postDataJSON();
      await route.fulfill({ status: 201, json: { data: { ok: true } } });
    });
    await page.goto("/essai-domaines-soutenir");
    await page.getByRole("button", { name: "Envoyer" }).click();
    await expect(page.getByText("Indiquez votre nom.")).toBeVisible();
    expect(envoi).toBeNull();

    await page.getByRole("textbox", { name: "Nom", exact: true }).fill("Fatou Sow");
    await page.getByLabel("Organisation").fill("Fondation Essai");
    await page.getByLabel("E-mail").fill("fatou@example.com");
    await page.getByLabel("Type de soutien").selectOption({ label: "Mécénat" });
    await page.getByLabel("Message").fill("Nous souhaitons soutenir l'école.");
    await page.getByRole("checkbox").check();
    await page.getByRole("button", { name: "Envoyer" }).click();

    const confirmation = page.getByRole("status");
    await expect(confirmation).toContainText("Merci ! Votre message a bien été envoyé. Nous vous répondrons rapidement.");
    await expect(confirmation).toBeFocused();
    expect(envoi).toMatchObject({ name: "Fatou Sow", organization: "Fondation Essai", email: "fatou@example.com", supportType: "sponsorship", consent: true });
  });

  test("« Formations de ce campus » ne montre que les formations de ce campus", async ({ page }) => {
    await page.goto("/essai-domaines-campus");
    const dakar = page.getByRole("region", { name: "Les formations à Dakar" });
    await expect(dakar.getByRole("heading", { name: "Essai — Dakar seulement" })).toBeVisible();
    await expect(dakar.getByText(/^Rentrée le \d{1,2}\s\S+\s\d{4}$/).first()).toBeVisible();
    await expect(dakar.getByRole("link", { name: "Candidater — Essai — Dakar seulement" }))
      .toHaveAttribute("href", "/candidater?campus=emsi-dakar&formation=essai-dakar-seulement");
    const saintLouis = page.getByRole("region", { name: "Les formations à Saint-Louis" });
    await expect(saintLouis).toBeVisible();
    await expect(saintLouis.getByText("Essai — Dakar seulement")).toHaveCount(0);
  });

  test("« Documents à télécharger » affiche le type et le poids, ouverts dans un nouvel onglet", async ({ page }) => {
    await page.goto("/essai-domaines-documents");
    const lien = page.getByRole("link", { name: /Brochure d'essai/ });
    await expect(lien).toHaveAttribute("target", "_blank");
    await expect(lien).toHaveAttribute("rel", /noopener/);
    await expect(lien).toHaveAccessibleName(/PDF/);
    await expect(page.getByText(/^PDF · \d+(,\d)? (octets|Ko|Mo)$/)).toBeVisible();
  });

  // Candidature : aucune candidature n'est envoyée, on s'arrête à la liste des formations.
  test("depuis la page du campus, la formation et le campus sont présélectionnés", async ({ page }) => {
    await page.goto("/candidater?campus=emsi-dakar&formation=essai-dakar-seulement");
    await expect(page.getByRole("radio", { name: /^Dakar/ })).toBeChecked();
    await expect(page.getByRole("radio", { name: /Essai — Dakar seulement/ })).toBeChecked();
    await expect(page.getByText(/n'est pas proposée à/)).toHaveCount(0);
  });

  test("à Saint-Louis, une formation réservée à Dakar n'est pas proposée", async ({ page }) => {
    await page.goto("/candidater?campus=emsi-saint-louis");
    await expect(page.getByRole("radio", { name: /^Saint-Louis/ })).toBeChecked();
    await expect(page.getByRole("radio", { name: /Essai — Dakar seulement/ })).toHaveCount(0);
    await page.getByRole("radio", { name: /^Dakar/ }).check();
    await expect(page.getByRole("radio", { name: /Essai — Dakar seulement/ })).toBeVisible();
    await page.getByRole("radio", { name: /^Saint-Louis/ }).check();
    await expect(page.getByRole("radio", { name: /Essai — Dakar seulement/ })).toHaveCount(0);
  });

  test("formation absente du campus demandé : message et aucune présélection", async ({ page }) => {
    await page.goto("/candidater?campus=emsi-saint-louis&formation=essai-dakar-seulement");
    await expect(page.getByRole("status")).toHaveText("Cette formation n'est pas proposée à Saint-Louis. Choisissez-en une autre ou changez de campus.");
    await expect(page.getByRole("radio", { name: /Essai — Dakar seulement/ })).toHaveCount(0);
    await expect(page.locator('input[name="offeringId"]:checked')).toHaveCount(0);
  });
});
