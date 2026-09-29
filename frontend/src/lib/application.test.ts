import { expect, test } from "@playwright/test";
import { offeringsForCampus, resolvePreselection } from "./application";
import type { Offering, Place } from "./types";

const place = (id: number, slug: string, city: string) => ({ id, slug, city, name: `EMSI ${city}`, kind: "campus" }) as Place;
const offering = (id: number, campusIds: number[]) => ({ id, campusIds }) as Offering;
const dakar = place(1, "emsi-dakar", "Dakar");
const sl = place(2, "emsi-saint-louis", "Saint-Louis");
const offerings = [offering(10, [1]), offering(11, [1, 2])];

test("filtre sur le campus choisi", () => {
    expect(offeringsForCampus(offerings, "2").map((o) => o.id)).toEqual([11]);
    expect(offeringsForCampus(offerings, "")).toEqual([]);
  });

test("présélectionne campus et formation", () => {
    expect(resolvePreselection({ offerings, campuses: [dakar, sl], campusSlug: "emsi-dakar", programOfferingIds: [10] })).toEqual({ campusId: "1", offeringId: "10", notice: undefined });
  });

test("formation absente du campus : message, pas de présélection", () => {
    const r = resolvePreselection({ offerings, campuses: [dakar, sl], campusSlug: "emsi-saint-louis", programOfferingIds: [10] });
    expect(r.offeringId).toBeUndefined();
    expect(r.campusId).toBe("2");
    expect(r.notice).toBe("Cette formation n'est pas proposée à Saint-Louis. Choisissez-en une autre ou changez de campus.");
  });

test("campus inconnu : rien, sans message", () => {
    const r = resolvePreselection({ offerings, campuses: [dakar, sl], campusSlug: "nulle-part", programOfferingIds: [11] });
    expect(r.campusId).toBeUndefined();
    expect(r.notice).toBeUndefined();
  });

test("un seul campus : pas de choix de campus", () => {
    expect(resolvePreselection({ offerings, campuses: [dakar], programOfferingIds: [10] })).toEqual({ campusId: undefined, offeringId: "10", notice: undefined });
  });

test("programme inconnu : ni présélection ni message", () => {
    expect(resolvePreselection({ offerings, campuses: [dakar, sl], campusSlug: "emsi-dakar", programOfferingIds: [] }).notice).toBeUndefined();
  });
