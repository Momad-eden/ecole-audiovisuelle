import type { MetadataRoute } from "next";

type Entry = { path: string; updatedAt: string | null };

/**
 * Entrées du plan du site : adresses fixes (routes dédiées) et adresses publiées dans l'admin.
 * Une adresse n'apparaît qu'une fois (ex. /emsi, route dédiée et page gérée dans l'admin) ;
 * la date de mise à jour de l'admin l'emporte.
 */
export function sitemapEntries(fixed: string[], entries: Entry[], siteUrl: string): MetadataRoute.Sitemap {
  const byPath = new Map<string, MetadataRoute.Sitemap[number]>();
  for (const path of fixed) byPath.set(path, { url: `${siteUrl}${path}` });
  for (const entry of entries) {
    byPath.set(entry.path, { url: `${siteUrl}${entry.path}`, lastModified: entry.updatedAt ?? undefined });
  }
  return [...byPath.values()];
}
