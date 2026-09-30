import type { Offering, Place } from "./types";

export const campusLabel = (campus: Place) => campus.city ?? campus.name;

/** « Saint-Louis » → « saint-louis » : forme de la ville dans les adresses (/emsi/saint-louis). */
const slugify = (value: string) => value.normalize("NFD").replace(/[̀-ͯ]/g, "").toLowerCase().replace(/[^a-z0-9]+/g, "-").replace(/^-+|-+$/g, "");

/** Campus désigné dans l'adresse : par son identifiant (emsi-saint-louis) ou par sa ville (saint-louis). */
const findCampus = (campuses: Place[], slug?: string) =>
  slug ? campuses.find((c) => c.slug === slug) ?? campuses.find((c) => c.city && slugify(c.city) === slug) : undefined;

const notOffered = (campus: Place) => `Cette formation n'est pas proposée à ${campusLabel(campus)}. Choisissez-en une autre ou changez de campus.`;

/** Formations proposées dans un campus (aucun campus choisi : aucune). */
export function offeringsForCampus(offerings: Offering[], campusId: string | undefined | null): Offering[] {
  if (!campusId) return [];
  return offerings.filter((o) => o.campusIds.includes(Number(campusId)));
}

/**
 * Présélection issue de l'adresse (`?campus=slug&formation=slug-de-programme`).
 * `programOfferingIds` : identifiants des offres ouvertes du programme demandé (vide si inconnu ou fermé).
 * Sans campus dans l'adresse (fiche formation), une formation ouverte dans un seul campus fixe ce campus ;
 * ouverte dans plusieurs, rien n'est imposé : le formulaire la resélectionne quand le candidat choisit son campus.
 */
export function resolvePreselection({ offerings, campuses, campusSlug, programOfferingIds }: { offerings: Offering[]; campuses: Place[]; campusSlug?: string; programOfferingIds: number[] }) {
  const single = campuses.length === 1;
  const candidates = offerings.filter((o) => programOfferingIds.includes(o.id));
  const offeringCampuses = campuses.filter((c) => candidates.some((o) => o.campusIds.includes(c.id)));
  const campus = single ? campuses[0] : findCampus(campuses, campusSlug) ?? (offeringCampuses.length === 1 ? offeringCampuses[0] : undefined);
  // Un seul campus : la liste n'est pas filtrée, la présélection non plus.
  const available = single ? candidates : campus ? candidates.filter((o) => o.campusIds.includes(campus.id)) : [];
  const offering = available[0];
  const notice = campus && !single && candidates.length > 0 && !offering ? notOffered(campus) : undefined;
  return {
    campusId: campus && !single ? String(campus.id) : undefined,
    offeringId: offering ? String(offering.id) : undefined,
    notice,
  };
}

/** Formation voulue (fiche formation) dans le campus que le candidat vient de choisir : son offre, sinon un message. */
export function wantedOffering({ offerings, campuses, campusId, wantedIds }: { offerings: Offering[]; campuses: Place[]; campusId?: string; wantedIds: number[] }) {
  const campus = campuses.find((c) => String(c.id) === campusId);
  if (!campus || wantedIds.length === 0) return { offeringId: undefined, notice: undefined };
  const offering = offeringsForCampus(offerings, campusId).find((o) => wantedIds.includes(o.id));
  return { offeringId: offering ? String(offering.id) : undefined, notice: offering ? undefined : notOffered(campus) };
}
