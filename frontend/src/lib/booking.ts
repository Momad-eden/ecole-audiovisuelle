import type { BookingType } from "./types";

/** Demandes possibles depuis le site : Impact Live Events (matériel, prestations) est retiré. Libellés : dictionnaire booking.types. */
export const BOOKING_TYPES = ["studio_session", "space_rental"] as const satisfies readonly BookingType[];

export type PublicBookingType = (typeof BOOKING_TYPES)[number];

/** Type du formulaire : celui fixé par le bloc s'il est encore proposé, sinon une séance au studio. */
export function publicBookingType(type: BookingType | null | undefined): PublicBookingType {
  return type && (BOOKING_TYPES as readonly string[]).includes(type) ? (type as PublicBookingType) : "studio_session";
}
