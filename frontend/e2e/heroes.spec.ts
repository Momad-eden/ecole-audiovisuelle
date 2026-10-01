import { expect, test } from "@playwright/test";

/*
 * Héros Projection, Studio et Cinéma, sur les pages d'essai créées par showcase.setup.ts
 * (php artisan emsi:hero-showcase) et supprimées à la fin des parcours.
 */

test("Projection : le cadre commence sous l'en-tête et un mot du titre est en couleur", async ({ page }) => {
  await page.goto("/essai-heros-projection");
  const header = await page.locator(".site-header").boundingBox();
  const frame = await page.getByTestId("projection-frame").boundingBox();
  expect(frame!.y).toBeGreaterThanOrEqual(header!.y + header!.height);
  await expect(page.getByRole("heading", { level: 1 })).toHaveText("Faites de votre passion un métier");
  await expect(page.getByRole("heading", { level: 1 }).locator("span")).toHaveText("un métier");
});

test("Studio : les points ont un nom et s'ouvrent au clavier", async ({ page, isMobile }) => {
  test.skip(isMobile, "Sur téléphone, les points sont remplacés par une liste.");
  await page.goto("/essai-heros-studio");
  const point = page.getByRole("button", { name: "Console 48 pistes" });
  await expect(point).toHaveAttribute("aria-expanded", "false");
  await point.focus();
  await expect(point).toHaveAttribute("aria-expanded", "true");
  await expect(page.getByRole("tooltip").filter({ hasText: "Console 48 pistes" })).toHaveCSS("opacity", "1");
});

test("Studio : sur téléphone, le matériel est listé sous le titre", async ({ page, isMobile }) => {
  test.skip(!isMobile, "Liste réservée aux petits écrans.");
  await page.goto("/essai-heros-studio");
  await expect(page.getByRole("list", { name: "Matériel du studio" }).getByRole("listitem")).toHaveCount(3);
});

test("Studio : rien ne joue avant le clic, le morceau choisi se lance au clic", async ({ page }) => {
  await page.goto("/essai-heros-studio");
  const player = page.getByTestId("studio-player");
  await expect(player.getByRole("button", { name: "Écouter « Voix témoin »" })).toBeVisible();
  await page.waitForTimeout(1500);
  await expect(player.getByRole("button", { name: /Mettre en pause/ })).toHaveCount(0);

  await player.getByRole("button", { name: "Mix témoin" }).click();
  await player.getByRole("button", { name: "Écouter « Mix témoin »" }).click();
  await expect(player.getByRole("button", { name: "Mettre en pause « Mix témoin »" })).toBeVisible();
});

test("Cinéma : la flèche droite passe à la diapositive suivante, le bouton pause arrête le défilement", async ({ page }) => {
  await page.goto("/essai-heros-cinema");
  const hero = page.getByTestId("cinema-hero");
  await expect(hero.getByRole("button", { name: "Diapositive 1 sur 3" })).toHaveAttribute("aria-current", "true");

  await hero.focus();
  await page.keyboard.press("ArrowRight");
  await expect(hero.getByRole("button", { name: "Diapositive 2 sur 3" })).toHaveAttribute("aria-current", "true");

  await hero.getByRole("button", { name: "Mettre le diaporama en pause" }).click();
  // Ni survol ni focus dans le héros : seule la pause doit retenir le défilement.
  await page.mouse.move(5, 5);
  await page.evaluate(() => (document.activeElement as HTMLElement | null)?.blur());
  await page.waitForTimeout(7000);
  await expect(hero.getByRole("button", { name: "Diapositive 2 sur 3" })).toHaveAttribute("aria-current", "true");
  await expect(hero.getByRole("button", { name: "Reprendre le diaporama" })).toBeVisible();
});

test("Cinéma : la page garde son titre principal quelle que soit la diapositive", async ({ page }) => {
  await page.goto("/essai-heros-cinema");
  await page.getByTestId("cinema-hero").getByRole("button", { name: "Diapositive 3 sur 3" }).click();
  await expect(page.getByRole("heading", { level: 1 })).toHaveText("Apprenez sur la plus grande scène du Sénégal");
});

test("Cinéma : en mouvement réduit, la diapositive ne change pas seule", async ({ page }) => {
  await page.emulateMedia({ reducedMotion: "reduce" });
  await page.goto("/essai-heros-cinema");
  await page.waitForTimeout(7000);
  await expect(page.getByTestId("cinema-hero").getByRole("button", { name: "Diapositive 1 sur 3" })).toHaveAttribute("aria-current", "true");
});

test("aucun défilement horizontal à 360 px de large", async ({ page }) => {
  await page.setViewportSize({ width: 360, height: 780 });
  for (const slug of ["essai-heros-projection", "essai-heros-studio", "essai-heros-cinema"]) {
    await page.goto(`/${slug}`);
    expect(await page.evaluate(() => document.documentElement.scrollWidth), slug).toBeLessThanOrEqual(360);
  }
});

test("Studio : la page ne télécharge ni les morceaux ni la photo d'origine avant qu'on les demande", async ({ page }) => {
  const heavy: string[] = [];
  page.on("request", (request) => {
    const url = request.url();
    if (url.endsWith(".wav") || url.includes("/storage/pages/essai-heros/studio_son.jpg")) heavy.push(url);
  });
  await page.goto("/essai-heros-studio");
  await expect(page.getByTestId("studio-player")).toBeVisible();
  await page.waitForTimeout(3000);
  expect(heavy).toEqual([]);
});

test("Cinéma : le diaporama avance même si la souris reste posée sur la photo", async ({ page, isMobile }) => {
  test.skip(isMobile, "Survol à la souris.");
  await page.goto("/essai-heros-cinema");
  const hero = page.getByTestId("cinema-hero");
  await page.mouse.move(1250, 350);
  await expect(hero.getByRole("button", { name: "Diapositive 2 sur 3" })).toHaveAttribute("aria-current", "true", { timeout: 9000 });
});

test("Cinéma : un titre avec un mot long n'est pas coupé à 360 px", async ({ page }) => {
  await page.setViewportSize({ width: 360, height: 780 });
  await page.goto("/essai-heros-cinema");
  await page.getByTestId("cinema-hero").getByRole("button", { name: "Diapositive 3 sur 3" }).click();
  const heading = page.getByRole("heading", { name: "Réalisez en régie audiovisuelle" });
  await expect(heading).toBeVisible();
  const [scroll, client] = await heading.evaluate((el) => [el.scrollWidth, el.clientWidth]);
  expect(scroll).toBeLessThanOrEqual(client + 1);
});

test("Studio : cliquer sur un point survolé garde son libellé ouvert (même geste qu'un toucher sur tablette)", async ({ page, isMobile }) => {
  test.skip(isMobile, "Points masqués sur téléphone.");
  await page.goto("/essai-heros-studio");
  const point = page.getByRole("button", { name: "Console 48 pistes" });
  await point.hover();
  await point.click();
  await page.waitForTimeout(300);
  await expect(point).toHaveAttribute("aria-expanded", "true");
});

test.describe("sur tablette tactile", () => {
  test.use({ viewport: { width: 820, height: 1180 }, hasTouch: true, isMobile: true });

  test("Studio : toucher un point affiche son libellé", async ({ page }) => {
    await page.goto("/essai-heros-studio");
    const point = page.getByRole("button", { name: "Console 48 pistes" });
    await point.tap();
    await page.waitForTimeout(400);
    await expect(point).toHaveAttribute("aria-expanded", "true");
  });
});

test("Film : grande phrase en h1, mots séparés, défilement des photos en pause au bouton", async ({ page }) => {
  await page.goto("/essai-heros-film");
  await expect(page.getByRole("heading", { level: 1 })).toHaveText("La culture comme héritage");
  const pause = page.getByTestId("film-hero").getByRole("button", { name: "Mettre la vidéo en pause" });
  await pause.click();
  await expect(page.getByTestId("film-hero").getByRole("button", { name: "Relancer la vidéo" })).toBeVisible();
});

test("Film : « Voir le film » ouvre la vidéo complète et Échap la referme", async ({ page }) => {
  await page.goto("/essai-heros-film");
  await page.getByRole("button", { name: "Voir le film" }).click();
  const dialog = page.getByRole("dialog", { name: "La culture comme héritage" });
  await expect(dialog.locator("iframe")).toHaveAttribute("src", /youtube-nocookie\.com\/embed\/dQw4w9WgXcQ/);
  await page.keyboard.press("Escape");
  await expect(dialog).toBeHidden();
});

test("Vitrine : une case s'agrandit, on passe à la suivante au clavier, Échap referme", async ({ page }) => {
  await page.goto("/essai-heros-film");
  const showcase = page.getByTestId("showcase");
  await showcase.getByRole("button", { name: "Agrandir : Sur scène" }).click();
  const viewer = page.getByRole("dialog", { name: "Sur scène et en coulisses" });
  await expect(viewer.getByText("1 sur 3")).toBeVisible();
  await expect(viewer.getByRole("link", { name: /Sur scène/ })).toHaveAttribute("href", "/centre-culturel/agenda");
  await page.keyboard.press("ArrowRight");
  await expect(viewer.getByText("2 sur 3")).toBeVisible();
  await page.keyboard.press("Escape");
  await expect(viewer).toBeHidden();
});

test("Chiffres clés : la valeur finale est lisible par les lecteurs d'écran", async ({ page }) => {
  await page.goto("/essai-heros-film");
  await expect(page.locator("dd .sr-only").filter({ hasText: "+300" })).toHaveCount(1);
  await page.getByText("diplômés").scrollIntoViewIfNeeded();
  await expect(page.locator("dd [aria-hidden]").filter({ hasText: "+300" })).toBeVisible({ timeout: 5000 });
});

test("En-tête : devient une capsule au défilement, se cache en descendant et revient en remontant", async ({ page }) => {
  await page.goto("/essai-heros-film");
  const header = page.locator(".site-header");
  await expect(header).not.toHaveAttribute("data-scrolled");
  // Premier défilement court (page prête, capsule), puis on descend franchement : l'en-tête se cache.
  await page.mouse.wheel(0, 200);
  await expect(header).toHaveAttribute("data-scrolled", "");
  await page.mouse.wheel(0, 1400);
  await expect.poll(async () => (await header.boundingBox())!.y).toBeLessThan(-40);
  await page.mouse.wheel(0, -300);
  await expect.poll(async () => (await header.boundingBox())!.y).toBe(0);
});

test("Manifeste : grande phrase avec mot mis en valeur, chiffre et bandeau de photos décoratif", async ({ page }) => {
  await page.goto("/essai-heros-film");
  const statement = page.getByTestId("statement");
  await expect(statement.locator("em.title-accent")).toHaveText("centre culturel");
  await expect(statement.locator("dd .sr-only")).toHaveText("3");
  await expect(statement.locator(".statement-ribbon")).toHaveAttribute("aria-hidden", "true");
});

test("Présentation institutionnelle : piliers numérotés et mot du fondateur", async ({ page }) => {
  await page.goto("/essai-heros-film");
  const institution = page.getByTestId("institution");
  await expect(institution.getByRole("heading", { level: 2 })).toHaveText("Former, créer, transmettre");
  await expect(institution.getByRole("listitem")).toHaveCount(3);
  await expect(institution.getByRole("heading", { level: 3, name: "Notre vision" })).toBeVisible();
  await expect(institution.locator("blockquote")).toContainText("Un rêve peut devenir réalité.");
  await expect(institution.locator("figcaption")).toContainText("Boubacar Tall");
});

test("Trois lieux après le film : trois cartes encadrées, avec une marge autour", async ({ page }) => {
  await page.goto("/essai-heros-film");
  const section = page.getByTestId("domains-block");
  await expect(section.getByRole("heading", { level: 2 })).toHaveText("Trois lieux, un même élan");
  const cards = section.getByTestId("domain-panel");
  await expect(cards).toHaveCount(3);
  await expect(section.getByRole("link", { name: "EMSI", exact: true })).toHaveAttribute("href", "/emsi");
  const box = (await cards.first().boundingBox())!;
  expect(box.x).toBeGreaterThanOrEqual(16);
  await expect(cards.first()).not.toHaveCSS("border-top-left-radius", "0px");
});
