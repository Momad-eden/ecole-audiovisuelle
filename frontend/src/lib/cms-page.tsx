import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { CurrentCrumb } from "@/components/layout/domain-crumb";
import { BlockRenderer } from "@/components/blocks/BlockRenderer";
import { api } from "@/lib/api";
import { pageMetadata } from "@/lib/metadata";
import { jsonLdScript, pageStructuredData } from "@/lib/structured-data";
import { siteUrl } from "@/lib/utils";

/** Page à blocs gérée dans l'admin, servie à une adresse fixe (ex. /events, qui a aussi des sous-pages). */
export async function cmsMetadata(slug: string): Promise<Metadata> {
  const page = await api.page(slug);
  return page ? pageMetadata(page) : {};
}

export async function CmsPageContent({ slug }: { slug: string }) {
  const page = await api.page(slug);
  if (!page) notFound();
  const { places } = await api.site();
  const jsonLd = pageStructuredData(page, `/${slug}`, places, siteUrl);
  return (
    <>
      {jsonLd && <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: jsonLdScript(jsonLd) }} />}
      <CurrentCrumb title={page.title} />
      <BlockRenderer blocks={page.blocks} path={`/${slug}`} title={page.title} />
    </>
  );
}
