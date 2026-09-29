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
