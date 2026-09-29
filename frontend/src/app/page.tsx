import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { BlockRenderer } from "@/components/blocks/BlockRenderer";
import { api } from "@/lib/api";
import { pageMetadata } from "@/lib/metadata";

export async function generateMetadata(): Promise<Metadata> {
  const [page, site] = await Promise.all([api.page("accueil"), api.site()]);
  const { settings } = site;
  return page ? { ...pageMetadata(page, settings.seoDescription || settings.description), title: { absolute: page.seo?.title || settings.seoTitle || settings.schoolName } } : {};
}

export default async function HomePage() {
  const [page, { settings }] = await Promise.all([api.page("accueil"), api.site()]);
  if (!page) notFound();

  return <BlockRenderer blocks={page.blocks} path="/" title={settings.schoolName || page.title} />;
}
