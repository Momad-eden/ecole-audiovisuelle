"use client";

import { useSyncExternalStore } from "react";

/** Sélection « Ma demande » (matériel, packs, services), gardée dans le navigateur jusqu'à l'envoi. */
export type QuoteItem = { kind: "equipment" | "pack" | "service"; id: number; name: string; quantity: number };

const KEY = "emsi-quote";
const EVENT = "emsi-quote-change";
const EMPTY: QuoteItem[] = [];
let cache: { raw: string | null; items: QuoteItem[] } = { raw: null, items: EMPTY };

function read(): QuoteItem[] {
  let raw: string | null = null;
  try {
    raw = localStorage.getItem(KEY);
  } catch {
    return EMPTY;
  }
  if (raw === cache.raw) return cache.items;
  let items: QuoteItem[] = EMPTY;
  try {
    const parsed = raw ? JSON.parse(raw) : [];
    items = Array.isArray(parsed) ? parsed.filter((i) => i && typeof i.id === "number" && typeof i.name === "string") : EMPTY;
  } catch {
    items = EMPTY;
  }
  cache = { raw, items };
  return items;
}

function write(items: QuoteItem[]) {
  try {
    localStorage.setItem(KEY, JSON.stringify(items));
  } catch {
    // Stockage indisponible : la sélection ne survivra pas au rechargement.
  }
  window.dispatchEvent(new Event(EVENT));
}

function subscribe(callback: () => void) {
  window.addEventListener(EVENT, callback);
  window.addEventListener("storage", callback);
  return () => {
    window.removeEventListener(EVENT, callback);
    window.removeEventListener("storage", callback);
  };
}

export function useQuote() {
  const items = useSyncExternalStore(subscribe, read, () => EMPTY);
  const same = (a: Pick<QuoteItem, "kind" | "id">, b: Pick<QuoteItem, "kind" | "id">) => a.kind === b.kind && a.id === b.id;

  return {
    items,
    count: items.reduce((total, item) => total + item.quantity, 0),
    has: (item: Pick<QuoteItem, "kind" | "id">) => items.some((i) => same(i, item)),
    add: (item: Omit<QuoteItem, "quantity">, quantity = 1) =>
      write(items.some((i) => same(i, item)) ? items : [...items, { ...item, quantity }]),
    setQuantity: (item: Pick<QuoteItem, "kind" | "id">, quantity: number) =>
      write(items.map((i) => (same(i, item) ? { ...i, quantity: Math.max(1, Math.min(999, Math.round(quantity) || 1)) } : i))),
    remove: (item: Pick<QuoteItem, "kind" | "id">) => write(items.filter((i) => !same(i, item))),
    clear: () => write([]),
  };
}
