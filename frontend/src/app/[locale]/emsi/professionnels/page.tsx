import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { BlockRenderer } from "@/components/blocks/BlockRenderer";
import { api } from "@/lib/api";
import type { Locale } from "@/lib/i18n/locales";
import { pageMetadata } from "@/lib/metadata";
import { localeAlternates } from "@/lib/i18n/alternates";
import { asLocale } from "@/lib/i18n/locales";

type Props = { params: Promise<{ locale: Locale }> };

const professionalPage = async (locale: Locale) => (await api.page("emsi/professionnels", locale)) ?? (await api.page("professionnels", locale));

async function baseMetadata({ params }: Props): Promise<Metadata> {
  const { locale } = await params;
  const page = await professionalPage(locale);
  return page ? pageMetadata(page, locale) : {};
}

export async function generateMetadata(props: Props): Promise<Metadata> {
  const p = await props.params;
  return { ...(await baseMetadata(props)), alternates: localeAlternates("/emsi/professionnels", asLocale(p.locale)) };
}

export default async function ProfessionalSpacePage({ params }: Props) {
  const { locale } = await params;
  const page = await professionalPage(locale);
  if (!page) notFound();

  return <div style={{ ["--accent" as string]: "var(--color-hmi)" }}><BlockRenderer blocks={page.blocks} path="/emsi/professionnels" title={page.title} locale={locale} contentLocale={page.contentLocale} /></div>;
}
