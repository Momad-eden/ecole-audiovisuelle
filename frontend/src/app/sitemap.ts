import type { MetadataRoute } from "next";
import { api } from "@/lib/api";
import { bilingualSitemapEntries } from "@/lib/sitemap";
import { siteUrl } from "@/lib/utils";

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  // Chaque adresse en français et en anglais (/en…), reliées par leurs liens de langue.
  const entries = await api.sitemap("fr");
  const fixed = ["/emsi", "/emsi/formations", "/emsi/realisations", "/centre-culturel", "/centre-culturel/agenda", "/actualites", "/candidater"];

  return bilingualSitemapEntries(fixed, entries, siteUrl);
}
