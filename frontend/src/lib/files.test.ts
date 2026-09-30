import { expect, test } from "@playwright/test";
import { fileLabel, fileSize } from "./files";

test("poids en octets, Ko et Mo, virgule française", () => {
  expect(fileSize(1)).toBe("1 octet");
  expect(fileSize(820)).toBe("820 octets");
  expect(fileSize(12_345)).toBe("12 Ko");
  expect(fileSize(1_258_291)).toBe("1,2 Mo");
  expect(fileSize(20 * 1024 * 1024)).toBe("20 Mo");
});

test("extension en capitales et poids", () => {
  expect(fileLabel("pdf", 1_258_291)).toBe("PDF · 1,2 Mo");
});
