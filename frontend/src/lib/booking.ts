import type { BookingType } from "./types";

/** Demandes possibles depuis le site : Impact Live Events (matériel, prestations) est retiré. */
export const BOOKING_TYPES = {
  studio_session: "Session au studio",
  space_rental: "Location de l'Espace Habib Faye",
} as const satisfies Partial<Record<BookingType, string>>;

export type PublicBookingType = keyof typeof BOOKING_TYPES;

/** Type du formulaire : celui fixé par le bloc s'il est encore proposé, sinon une séance au studio. */
export function publicBookingType(type: BookingType | null | undefined): PublicBookingType {
  return type && type in BOOKING_TYPES ? (type as PublicBookingType) : "studio_session";
}
