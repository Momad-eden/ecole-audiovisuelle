import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { CurrentCrumb } from "@/components/layout/domain-crumb";
import { BlockRenderer } from "@/components/blocks/BlockRenderer";
import { api } from "@/lib/api";
import type { Locale } from "@/lib/i18n/locales";
import { pageMetadata } from "@/lib/metadata";
import { jsonLdScript, pageStructuredData } from "@/lib/structured-data";
import { siteUrl } from "@/lib/utils";

/** Page à blocs gérée dans l'admin, servie par une route dédiée à une adresse fixe (ex. /maison-habib-faye/studio). */
export async function cmsMetadata(slug: string, locale: Locale): Promise<Metadata> {
  const page = await api.page(slug, locale);
  return page ? pageMetadata(page, locale) : {};
}

export async function CmsPageContent({ slug, locale }: { slug: string; locale: Locale }) {
  const page = await api.page(slug, locale);
  if (!page) notFound();
  const { places } = await api.site(locale);
  const jsonLd = pageStructuredData(page, `/${slug}`, places, siteUrl, locale);
  return (
    <>
      {jsonLd && <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: jsonLdScript(jsonLd) }} />}
      <CurrentCrumb title={page.title} />
      <BlockRenderer blocks={page.blocks} path={`/${slug}`} title={page.title} locale={locale} contentLocale={page.contentLocale} />
    </>
  );
}
