import type { Metadata } from "next";
import { notFound, permanentRedirect, redirect } from "next/navigation";
import { CurrentCrumb } from "@/components/layout/domain-crumb";
import { DomainChrome } from "@/components/layout/DomainChrome";
import { BlockRenderer } from "@/components/blocks/BlockRenderer";
import { api } from "@/lib/api";
import { pageMetadata } from "@/lib/metadata";
import { jsonLdScript, pageStructuredData } from "@/lib/structured-data";
import { siteUrl } from "@/lib/utils";

type Props = { params: Promise<{ slug: string[] }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug } = await params;
  const page = await api.page(slug.join("/"));
  return page ? pageMetadata(page) : {};
}

/** Pages gérées dans l'administration (école, contact, pages libres…) et anciennes adresses redirigées. */
export default async function CmsPage({ params }: Props) {
  const { slug } = await params;
  const path = slug.join("/");
  const page = await api.page(path);

  if (!page) {
    const redirection = (await api.redirects()).find((r) => r.from === `/${path}`);
    if (redirection) {
      if (redirection.status === 301) permanentRedirect(redirection.to);
      redirect(redirection.to);
    }
    notFound();
  }

  const { menus, domains, places } = await api.site();
  const jsonLd = pageStructuredData(page, `/${path}`, places, siteUrl);
  return (
    <DomainChrome domain={page.domain ?? "general"} site={{ menus, domains }} path={`/${path}`} title={page.title}>
      {jsonLd && <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: jsonLdScript(jsonLd) }} />}
      <CurrentCrumb title={page.title} />
      <BlockRenderer blocks={page.blocks} path={`/${path}`} title={page.title} />
    </DomainChrome>
  );
}
