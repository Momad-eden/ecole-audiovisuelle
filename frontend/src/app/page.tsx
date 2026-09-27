import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { BlockRenderer } from "@/components/blocks/BlockRenderer";
import { api } from "@/lib/api";
import { pageMetadata } from "@/lib/metadata";

export async function generateMetadata(): Promise<Metadata> {
  const page = await api.page("accueil");
  return page ? { ...pageMetadata(page), title: { absolute: page.seo?.title || (await api.site()).settings.schoolName } } : {};
}

export default async function HomePage() {
  const page = await api.page("accueil");
  if (!page) notFound();

  return <BlockRenderer blocks={page.blocks} />;
}
