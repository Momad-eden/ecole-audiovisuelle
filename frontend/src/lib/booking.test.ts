import { expect, test } from "@playwright/test";
import { BOOKING_TYPES, publicBookingType } from "./booking";

test("le formulaire public ne propose que le studio et la location d'espace", () => {
  expect(Object.keys(BOOKING_TYPES)).toEqual(["studio_session", "space_rental"]);
});

test("type par défaut : celui du bloc, sinon séance studio (jamais un service retiré)", () => {
  expect(publicBookingType(undefined)).toBe("studio_session");
  expect(publicBookingType(null)).toBe("studio_session");
  expect(publicBookingType("space_rental")).toBe("space_rental");
  expect(publicBookingType("equipment_rental")).toBe("studio_session");
  expect(publicBookingType("event_service")).toBe("studio_session");
});
