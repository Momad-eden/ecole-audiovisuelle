import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { BlockRenderer } from "@/components/blocks/BlockRenderer";
import { api } from "@/lib/api";
import type { Locale } from "@/lib/i18n/locales";
import { pageMetadata } from "@/lib/metadata";
import { localeAlternates } from "@/lib/i18n/alternates";
import { asLocale } from "@/lib/i18n/locales";

type Props = { params: Promise<{ locale: Locale }> };

async function baseMetadata({ params }: Props): Promise<Metadata> {
  const { locale } = await params;
  const [page, site] = await Promise.all([api.page("accueil", locale), api.site(locale)]);
  const { settings } = site;
  return page ? { ...pageMetadata(page, settings.seoDescription || settings.description), title: { absolute: page.seo?.title || settings.seoTitle || settings.schoolName } } : {};
}

export async function generateMetadata(props: Props): Promise<Metadata> {
  const p = await props.params;
  return { ...(await baseMetadata(props)), alternates: localeAlternates("/", asLocale(p.locale)) };
}

export default async function HomePage({ params }: Props) {
  const { locale } = await params;
  const [page, { settings }] = await Promise.all([api.page("accueil", locale), api.site(locale)]);
  if (!page) notFound();

  return <BlockRenderer blocks={page.blocks} path="/" title={settings.schoolName || page.title} locale={locale} contentLocale={page.contentLocale} />;
}
