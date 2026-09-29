import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { BlockRenderer } from "@/components/blocks/BlockRenderer";
import { api } from "@/lib/api";
import { pageMetadata } from "@/lib/metadata";

export async function generateMetadata(): Promise<Metadata> {
  const page = (await api.page("emsi/professionnels")) ?? (await api.page("professionnels"));
  return page ? pageMetadata(page) : {};
}

export default async function ProfessionalSpacePage() {
  const page = (await api.page("emsi/professionnels")) ?? (await api.page("professionnels"));
  if (!page) notFound();

  return <div style={{ ["--accent" as string]: "var(--color-hmi)" }}><BlockRenderer blocks={page.blocks} path="/emsi/professionnels" /></div>;
}
