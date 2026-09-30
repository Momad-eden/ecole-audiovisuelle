import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { BlockRenderer } from "@/components/blocks/BlockRenderer";
import { api } from "@/lib/api";
import type { Locale } from "@/lib/i18n/locales";
import { pageMetadata } from "@/lib/metadata";

type Props = { params: Promise<{ locale: Locale }> };

const professionalPage = async (locale: Locale) => (await api.page("emsi/professionnels", locale)) ?? (await api.page("professionnels", locale));

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const page = await professionalPage((await params).locale);
  return page ? pageMetadata(page) : {};
}

export default async function ProfessionalSpacePage({ params }: Props) {
  const page = await professionalPage((await params).locale);
  if (!page) notFound();

  return <div style={{ ["--accent" as string]: "var(--color-hmi)" }}><BlockRenderer blocks={page.blocks} path="/emsi/professionnels" title={page.title} /></div>;
}
