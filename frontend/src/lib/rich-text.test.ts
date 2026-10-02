import { expect, test } from "@playwright/test";
import { sanitizeRichText } from "./rich-text";

const link = (href: string) => `<p><a href="${href}">lien</a></p>`;
const hrefOf = (html: string) => html.match(/href="([^"]*)"/)?.[1];

test("en anglais, les liens internes du texte riche passent sous /en", () => {
  expect(hrefOf(sanitizeRichText(link("/emsi/dakar"), "en"))).toBe("/en/emsi/dakar");
  expect(hrefOf(sanitizeRichText(link("/"), "en"))).toBe("/en");
  expect(hrefOf(sanitizeRichText(link("/candidater?campus=emsi-dakar#form"), "en"))).toBe("/en/candidater?campus=emsi-dakar#form");
});

test("médias, API, liens déjà anglais, ancres, e-mails et sites externes restent tels quels", () => {
  for (const href of ["/storage/docs/plaquette.pdf", "/api/v1/public/site", "/en/emsi", "#programme", "mailto:contact@emsi.sn", "tel:+221770000000"]) {
    expect(hrefOf(sanitizeRichText(link(href), "en"))).toBe(href);
  }
  const external = sanitizeRichText(link("https://example.org/page"), "en");
  expect(hrefOf(external)).toBe("https://example.org/page");
  expect(external).toContain('target="_blank"');
  expect(external).toContain('rel="noopener noreferrer"');
});

test("en français, rien ne change", () => {
  expect(hrefOf(sanitizeRichText(link("/emsi/dakar"), "fr"))).toBe("/emsi/dakar");
  expect(hrefOf(sanitizeRichText(link("/emsi/dakar")))).toBe("/emsi/dakar");
});

test("le nettoyage reste en place : script retiré, style libre refusé", () => {
  const html = sanitizeRichText('<p style="color:red;text-align:center">a</p><script>alert(1)</script><a href="javascript:alert(1)">x</a>', "en");
  expect(html).not.toContain("script");
  expect(html).not.toContain("color");
  expect(html).toContain("text-align:center");
  expect(html).not.toContain("javascript");
});
