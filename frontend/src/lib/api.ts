import "server-only";
import type { Locale } from "./i18n/locales";
import type { AgendaEvent, Artwork, EquipmentCategory, EquipmentItem, Exhibition, NewsItem, Offering, Page, Paginated, Program, RentalPack, RoomSummary, Service, Site } from "./types";

const API_URL = process.env.API_URL ?? "http://127.0.0.1:8000";

/** Toutes les données publiques partagent l'étiquette « content », invalidée par Laravel à chaque publication. */
export const CONTENT_TAG = "content";

export class NotFoundError extends Error {}

/** Chaque appel précise la langue (?locale=) : l'API renvoie les textes anglais, ou le français à défaut. */
async function get<T>(path: string, locale: Locale, init?: { preview?: boolean }): Promise<T> {
  const response = await fetch(`${API_URL}/api/v1/public/${path}${path.includes("?") ? "&" : "?"}locale=${locale}`, {
    headers: { Accept: "application/json" },
    ...(init?.preview ? { cache: "no-store" as const } : { next: { tags: [CONTENT_TAG], revalidate: 3600 } }),
  });

  if (response.status === 404 || response.status === 403) {
    throw new NotFoundError(path);
  }
  if (!response.ok) {
    throw new Error(`API ${response.status} sur ${path}`);
  }

  return response.json() as Promise<T>;
}

/** Renvoie null si la ressource n'existe pas (pour appeler notFound() dans la page). */
async function maybe<T>(promise: Promise<T>): Promise<T | null> {
  try {
    return await promise;
  } catch (error) {
    if (error instanceof NotFoundError) return null;
    throw error;
  }
}

const data = async <T>(promise: Promise<{ data: T }>) => (await promise).data;

export const api = {
  site: (locale: Locale) => data(get<{ data: Site }>("site", locale)),
  page: (slug: string, locale: Locale) => maybe(data(get<{ data: Page }>(`pages/${slug}`, locale))),
  preview: (token: string, locale: Locale) => maybe(data(get<{ data: Page }>(`preview?token=${encodeURIComponent(token)}`, locale, { preview: true }))),
  rooms: (locale: Locale) => data(get<{ data: RoomSummary[] }>("rooms", locale)),
  room: (slug: string, locale: Locale) => maybe(data(get<{ data: RoomSummary }>(`rooms/${slug}`, locale))),
  artworks: (query: string, locale: Locale) => get<Paginated<Artwork>>(`artworks${query ? `?${query}` : ""}`, locale),
  artwork: (slug: string, locale: Locale) => maybe(data(get<{ data: Artwork }>(`artworks/${slug}`, locale))),
  exhibitions: (state: string | undefined, locale: Locale) => data(get<{ data: Exhibition[] }>(`exhibitions${state ? `?state=${state}` : ""}`, locale)),
  exhibition: (slug: string, locale: Locale) => maybe(data(get<{ data: Exhibition }>(`exhibitions/${slug}`, locale))),
  programs: (audience: "school" | "professional" | undefined, locale: Locale) => data(get<{ data: Program[] }>(`programs${audience ? `?audience=${audience}` : ""}`, locale)),
  program: (slug: string, locale: Locale) => maybe(data(get<{ data: Program }>(`programs/${slug}`, locale))),
  offerings: (audience: "school" | "professional" | undefined, locale: Locale) => data(get<{ data: Offering[] }>(`offerings${audience ? `?audience=${audience}` : ""}`, locale)),
  news: (page: number, locale: Locale) => get<Paginated<NewsItem>>(`news?page=${page}`, locale),
  newsItem: (slug: string, locale: Locale) => maybe(data(get<{ data: NewsItem }>(`news/${slug}`, locale))),
  services: (activity: string | undefined, locale: Locale) => data(get<{ data: Service[] }>(`services${activity ? `?activity=${activity}` : ""}`, locale)),
  equipmentCategories: (locale: Locale) => data(get<{ data: EquipmentCategory[] }>("equipment-categories", locale)),
  equipment: (query: string, locale: Locale) => data(get<{ data: EquipmentItem[] }>(`equipment${query ? `?${query}` : ""}`, locale)),
  equipmentItem: (slug: string, locale: Locale) => maybe(data(get<{ data: EquipmentItem }>(`equipment/${slug}`, locale))),
  packs: (locale: Locale) => data(get<{ data: RentalPack[] }>("packs", locale)),
  agenda: (query: string, locale: Locale) => data(get<{ data: AgendaEvent[] }>(`agenda${query ? `?${query}` : ""}`, locale)),
  agendaEvent: (slug: string, locale: Locale) => maybe(data(get<{ data: AgendaEvent }>(`agenda/${slug}`, locale))),
  redirects: (locale: Locale) => data(get<{ data: { from: string; to: string; status: number }[] }>("redirects", locale)),
  sitemap: (locale: Locale) => data(get<{ data: { path: string; updatedAt: string | null }[] }>("sitemap", locale)),
};
