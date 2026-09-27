import type { Metadata } from "next";
import Link from "next/link";
import { NewsCard } from "@/components/NewsCard";
import { Section } from "@/components/ui/Section";
import { api } from "@/lib/api";

export const metadata: Metadata = { title: "Actualités", description: "Les actualités de l'EMSI : vie de l'école, événements, Espace Professionnels." };

type Props = { searchParams: Promise<{ page?: string }> };

export default async function NewsPage({ searchParams }: Props) {
  const page = Math.max(1, Number((await searchParams).page ?? 1) || 1);
  const news = await api.news(page);

  return (
    <>
      <section className="beam pb-4 pt-24">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <p className="cartel mb-4">Journal</p>
          <h1 className="font-display text-5xl font-medium sm:text-7xl">Actualités</h1>
        </div>
      </section>
      <Section>
        {news.data.length === 0 ? (
          <p className="text-ink-muted">Aucune actualité pour le moment.</p>
        ) : (
          <div className="grid gap-x-8 gap-y-14 md:grid-cols-2 lg:grid-cols-3">
            {news.data.map((item) => <NewsCard key={item.id} news={item} />)}
          </div>
        )}
        {news.meta.last_page > 1 && (
          <nav aria-label="Pagination" className="mt-16 flex justify-center gap-3">
            {page > 1 && <Link href={`/actualites?page=${page - 1}`} className="rounded-full border border-line px-5 py-2">Précédent</Link>}
            <span className="cartel self-center">Page {page} / {news.meta.last_page}</span>
            {page < news.meta.last_page && <Link href={`/actualites?page=${page + 1}`} className="rounded-full border border-line px-5 py-2">Suivant</Link>}
          </nav>
        )}
      </Section>
    </>
  );
}
