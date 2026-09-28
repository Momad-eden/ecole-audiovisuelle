import type { Block, BookingType } from "@/lib/types";
import { FORM_ANCHOR } from "./ImpactBlocks";

const items = (block: Block) => (Array.isArray(block.data.items) ? (block.data.items as { audio?: { url?: string } | null }[]) : []);

/** Sections réellement affichées par une page (identifiants que les liens « #… » peuvent viser). */
export function pageAnchors(blocks: Block[]): Set<string> {
  const anchors = new Set<string>();
  for (const block of blocks) {
    if (block.type === "productions" && items(block).some((item) => item.audio?.url)) anchors.add("productions");
    if (block.type === "booking_form") anchors.add(FORM_ANCHOR[(block.data.bookingType as BookingType) ?? "equipment_rental"]);
    if (block.type === "agenda" && block.data.scope !== "references" && items(block).length > 0) anchors.add("programmation");
    if (block.type === "rooms" && items(block).length > 0) anchors.add("univers");
  }
  return anchors;
}

/**
 * Un bouton qui vise une section de la page même (« #productions », « /studio#productions »)
 * n'est affiché que si la section existe : jamais de bouton qui ne mène nulle part.
 */
export function leadsSomewhere(url: string, path: string, anchors: Set<string>): boolean {
  const [target, hash] = url.split("#");
  if (hash === undefined || (target !== "" && target !== path)) return true;
  return anchors.has(hash);
}
