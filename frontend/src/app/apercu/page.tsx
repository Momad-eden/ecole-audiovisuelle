import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { BlockRenderer } from "@/components/blocks/BlockRenderer";
import { api } from "@/lib/api";

export const metadata: Metadata = { title: "Aperçu", robots: { index: false, follow: false } };

type Props = { searchParams: Promise<{ token?: string }> };

/** Aperçu d'un brouillon depuis l'administration (lien signé, valable une heure). */
export default async function PreviewPage({ searchParams }: Props) {
  const { token } = await searchParams;
  const page = token ? await api.preview(token) : null;
  if (!page) notFound();

  return (
    <>
      <div role="status" className="sticky top-16 z-30 bg-amber px-4 py-2 text-center text-sm font-semibold text-night">
        Aperçu du brouillon « {page.title} » — cette version n&apos;est pas encore publiée.
      </div>
      <BlockRenderer blocks={page.blocks} />
    </>
  );
}
