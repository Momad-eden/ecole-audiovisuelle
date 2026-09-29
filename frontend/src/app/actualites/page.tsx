import type { Metadata } from "next";
import Link from "next/link";
import { NewsCard } from "@/components/NewsCard";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { EmptyState } from "@/components/ui/EmptyState";
import { PageHeader } from "@/components/ui/PageHeader";
import { Section } from "@/components/ui/Section";
import { api } from "@/lib/api";

export const metadata: Metadata = { title: "Actualités", description: "Les actualités de l'EMSI : vie de l'école, événements, Espace Professionnels." };

type Props = { searchParams: Promise<{ page?: string }> };

export default async function NewsPage({ searchParams }: Props) {
  const page = Math.max(1, Number((await searchParams).page ?? 1) || 1);
  const news = await api.news(page);

  return (
    <>
      <PageHeader eyebrow="Journal" title="Actualités" text="La vie de l'EMSI à Dakar et à Saint-Louis, d'Impact Live et de l'Espace Habib Faye." />
      <Section className="pt-0 sm:pt-0">
        {news.data.length === 0 ? (
          <EmptyState title="Les premières actualités arrivent." text="Rentrées, portes ouvertes, concerts, réalisations d'étudiants : suivez-nous en attendant sur l'agenda.">
            <ButtonLink href="/maison-habib-faye/agenda">Voir l&apos;agenda</ButtonLink>
            <ButtonLink href="/emsi" variant="secondary">Découvrir les univers</ButtonLink>
          </EmptyState>
        ) : (
          <div className="grid gap-x-8 gap-y-14 md:grid-cols-2 lg:grid-cols-3">
            {news.data.map((item) => <NewsCard key={item.id} news={item} />)}
          </div>
        )}
        {news.meta.last_page > 1 && (
          <nav aria-label="Pagination" className="mt-16 flex justify-center gap-3">
            {page > 1 && <Link href={`/actualites?page=${page - 1}`} className="inline-flex min-h-11 items-center rounded-full border border-line px-5 hover:border-ink/40">Précédent</Link>}
            <span className="cartel self-center">Page {page} / {news.meta.last_page}</span>
            {page < news.meta.last_page && <Link href={`/actualites?page=${page + 1}`} className="inline-flex min-h-11 items-center rounded-full border border-line px-5 hover:border-ink/40">Suivant</Link>}
          </nav>
        )}
      </Section>
    </>
  );
}
