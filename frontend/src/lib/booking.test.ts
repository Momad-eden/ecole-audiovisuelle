import { expect, test } from "@playwright/test";
import { BOOKING_TYPES, publicBookingType } from "./booking";
import { en } from "./i18n/dictionaries/en";
import { fr } from "./i18n/dictionaries/fr";

test("le formulaire public ne propose que le studio et la location d'espace", () => {
  expect(BOOKING_TYPES).toEqual(["studio_session", "space_rental"]);
  // Chaque type proposé a son libellé dans les deux langues.
  expect(Object.keys(fr.booking.types)).toEqual([...BOOKING_TYPES]);
  expect(Object.keys(en.booking.types)).toEqual([...BOOKING_TYPES]);
});

test("type par défaut : celui du bloc, sinon séance studio (jamais un service retiré)", () => {
  expect(publicBookingType(undefined)).toBe("studio_session");
  expect(publicBookingType(null)).toBe("studio_session");
  expect(publicBookingType("space_rental")).toBe("space_rental");
  expect(publicBookingType("equipment_rental")).toBe("studio_session");
  expect(publicBookingType("event_service")).toBe("studio_session");
});
