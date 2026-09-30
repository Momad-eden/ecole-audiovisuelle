import type { MetadataRoute } from "next";
import { api } from "@/lib/api";
import { sitemapEntries } from "@/lib/sitemap";
import { siteUrl } from "@/lib/utils";

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  const entries = await api.sitemap();
  const fixed = ["/emsi", "/emsi/formations", "/emsi/realisations", "/maison-habib-faye", "/maison-habib-faye/agenda", "/actualites", "/candidater"];

  return sitemapEntries(fixed, entries, siteUrl);
}
