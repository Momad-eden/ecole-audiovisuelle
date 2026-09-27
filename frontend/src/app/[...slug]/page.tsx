import type { Metadata } from "next";
import { notFound, permanentRedirect, redirect } from "next/navigation";
import { BlockRenderer } from "@/components/blocks/BlockRenderer";
import { api } from "@/lib/api";
import { pageMetadata } from "@/lib/metadata";

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

  return <BlockRenderer blocks={page.blocks} />;
}
