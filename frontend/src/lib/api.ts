import "server-only";
import type { Artwork, Exhibition, NewsItem, Offering, Page, Paginated, Program, RoomSummary, Site } from "./types";

const API_URL = process.env.API_URL ?? "http://127.0.0.1:8000";

/** Toutes les données publiques partagent l'étiquette « content », invalidée par Laravel à chaque publication. */
export const CONTENT_TAG = "content";

export class NotFoundError extends Error {}

async function get<T>(path: string, init?: { preview?: boolean }): Promise<T> {
  const response = await fetch(`${API_URL}/api/v1/public/${path}`, {
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
  site: () => data(get<{ data: Site }>("site")),
  page: (slug: string) => maybe(data(get<{ data: Page }>(`pages/${slug}`))),
  preview: (token: string) => maybe(data(get<{ data: Page }>(`preview?token=${encodeURIComponent(token)}`, { preview: true }))),
  rooms: () => data(get<{ data: RoomSummary[] }>("rooms")),
  room: (slug: string) => maybe(data(get<{ data: RoomSummary }>(`rooms/${slug}`))),
  artworks: (query = "") => get<Paginated<Artwork>>(`artworks${query ? `?${query}` : ""}`),
  artwork: (slug: string) => maybe(data(get<{ data: Artwork }>(`artworks/${slug}`))),
  exhibitions: (state?: string) => data(get<{ data: Exhibition[] }>(`exhibitions${state ? `?state=${state}` : ""}`)),
  exhibition: (slug: string) => maybe(data(get<{ data: Exhibition }>(`exhibitions/${slug}`))),
  programs: (audience?: "school" | "professional") => data(get<{ data: Program[] }>(`programs${audience ? `?audience=${audience}` : ""}`)),
  program: (slug: string) => maybe(data(get<{ data: Program }>(`programs/${slug}`))),
  offerings: (audience?: "school" | "professional") => data(get<{ data: Offering[] }>(`offerings${audience ? `?audience=${audience}` : ""}`)),
  news: (page = 1) => get<Paginated<NewsItem>>(`news?page=${page}`),
  newsItem: (slug: string) => maybe(data(get<{ data: NewsItem }>(`news/${slug}`))),
  redirects: () => data(get<{ data: { from: string; to: string; status: number }[] }>("redirects")),
  sitemap: () => data(get<{ data: { path: string; updatedAt: string | null }[] }>("sitemap")),
};
