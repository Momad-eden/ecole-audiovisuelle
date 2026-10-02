import { expect, test } from "@playwright/test";
import { formatDate, formatMoney, formatNumber } from "./format";

/** Ancien format des montants (src/lib/utils.ts, avant le site bilingue) : le français doit rester identique octet pour octet. */
const legacyFcfa = (amount: number) => `${new Intl.NumberFormat("fr-FR", { maximumFractionDigits: 0 }).format(amount).replace(/ /g, " ")} FCFA`;
const legacyDate = (iso: string, options: Intl.DateTimeFormatOptions = { day: "numeric", month: "long", year: "numeric" }) =>
  new Intl.DateTimeFormat("fr-FR", { timeZone: "Africa/Dakar", ...options }).format(new Date(iso));

test("dates : « 5 octobre 2026 » en français, « 5 October 2026 » en anglais britannique", () => {
  expect(formatDate("2026-10-05", "fr")).toBe("5 octobre 2026");
  expect(formatDate("2026-10-05", "en")).toBe("5 October 2026");
  expect(formatDate(null, "en")).toBe("");
  expect(formatDate(undefined, "fr")).toBe("");
});

test("dates en français identiques à l'ancien format, options comprises", () => {
  for (const iso of ["2026-10-05", "2027-01-15T18:30:00+00:00", "2026-12-31T23:30:00Z"]) {
    expect(formatDate(iso, "fr")).toBe(legacyDate(iso));
    expect(formatDate(iso, "fr", { dateStyle: "full", timeStyle: "short" })).toBe(legacyDate(iso, { dateStyle: "full", timeStyle: "short" }));
  }
  expect(formatDate("2027-01-15T18:30:00+00:00", "en", { weekday: "long", hour: "2-digit", minute: "2-digit" })).toBe("Friday 18:30");
});

test("montants toujours en FCFA : français inchangé, anglais avec virgules", () => {
  expect(formatMoney(1_250_000, "fr")).toBe("1 250 000 FCFA");
  expect(formatMoney(1_250_000, "en")).toBe("1,250,000 FCFA");
  expect(formatMoney(1_250_000, "en")).toContain("FCFA");
  for (const amount of [0, 950, 25_000, 1_250_000, 123_456_789, 1250.6]) expect(formatMoney(amount, "fr")).toBe(legacyFcfa(amount));
});

test("nombres selon la langue", () => {
  expect(formatNumber(1_250_000, "fr")).toBe("1 250 000");
  expect(formatNumber(1_250_000, "en")).toBe("1,250,000");
  expect(formatNumber(1.5, "fr", { maximumFractionDigits: 1 })).toBe("1,5");
  expect(formatNumber(1.5, "en", { maximumFractionDigits: 1 })).toBe("1.5");
});
