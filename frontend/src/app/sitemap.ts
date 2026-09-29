import type { MetadataRoute } from "next";
import { api } from "@/lib/api";
import { siteUrl } from "@/lib/utils";

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  const entries = await api.sitemap();
  const fixed = ["/emsi", "/emsi/formations", "/emsi/realisations", "/maison-habib-faye", "/maison-habib-faye/agenda", "/actualites", "/candidater"];

  return [
    ...fixed.map((path) => ({ url: `${siteUrl}${path}` })),
    ...entries.map((entry) => ({ url: `${siteUrl}${entry.path}`, lastModified: entry.updatedAt ?? undefined })),
  ];
}
