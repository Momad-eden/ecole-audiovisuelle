import { expect, test } from "@playwright/test";
import { routeLocale } from "./routing";

test("sans préfixe : réécriture interne vers le français", () => {
  expect(routeLocale("/")).toEqual({ action: "rewrite", pathname: "/fr" });
  expect(routeLocale("/emsi/dakar")).toEqual({ action: "rewrite", pathname: "/fr/emsi/dakar" });
  expect(routeLocale("/entreprises")).toEqual({ action: "rewrite", pathname: "/fr/entreprises" });
  expect(routeLocale("/opengraph-image")).toEqual({ action: "rewrite", pathname: "/fr/opengraph-image" });
});

test("/en passe tel quel", () => {
  expect(routeLocale("/en")).toEqual({ action: "next" });
  expect(routeLocale("/en/emsi/dakar")).toEqual({ action: "next" });
});

test("/fr explicite : redirection vers l'adresse sans préfixe", () => {
  expect(routeLocale("/fr")).toEqual({ action: "redirect", pathname: "/" });
  expect(routeLocale("/fr/emsi")).toEqual({ action: "redirect", pathname: "/emsi" });
  expect(routeLocale("/fr/emsi/dakar")).toEqual({ action: "redirect", pathname: "/emsi/dakar" });
});

test("fichiers, API, médias et ressources de Next ne sont pas aiguillés", () => {
  for (const path of ["/api/preview", "/api/v1/public/site", "/api", "/_next/static/chunks/a.js", "/_next/image", "/storage/a.png", "/favicon.ico", "/robots.txt", "/sitemap.xml", "/file.svg", "/fr/logo.png"]) {
    expect(routeLocale(path)).toEqual({ action: "next" });
  }
});
