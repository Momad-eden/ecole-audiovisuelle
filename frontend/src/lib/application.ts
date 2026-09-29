import type { Offering, Place } from "./types";

export const campusLabel = (campus: Place) => campus.city ?? campus.name;

/** Formations proposées dans un campus (aucun campus choisi : aucune). */
export function offeringsForCampus(offerings: Offering[], campusId: string | undefined | null): Offering[] {
  if (!campusId) return [];
  return offerings.filter((o) => o.campusIds.includes(Number(campusId)));
}

/**
 * Présélection issue de l'adresse (`?campus=slug&formation=slug-de-programme`).
 * `programOfferingIds` : identifiants des offres ouvertes du programme demandé (vide si inconnu ou fermé).
 */
export function resolvePreselection({ offerings, campuses, campusSlug, programOfferingIds }: { offerings: Offering[]; campuses: Place[]; campusSlug?: string; programOfferingIds: number[] }) {
  const campus = campuses.length === 1 ? campuses[0] : campuses.find((c) => c.slug === campusSlug);
  const candidates = offerings.filter((o) => programOfferingIds.includes(o.id));
  const single = campuses.length === 1;
  // Un seul campus : la liste n'est pas filtrée, la présélection non plus.
  const available = campus && !single ? candidates.filter((o) => o.campusIds.includes(campus.id)) : candidates;
  const offering = available[0];
  const notice = campus && !single && candidates.length > 0 && !offering ? `Cette formation n'est pas proposée à ${campusLabel(campus)}. Choisissez-en une autre ou changez de campus.` : undefined;
  return {
    campusId: campus && campuses.length > 1 ? String(campus.id) : undefined,
    offeringId: offering ? String(offering.id) : undefined,
    notice,
  };
}
