import type { Metadata } from "next";
import Link from "next/link";
import { ArtworkGrid } from "@/components/museum/ArtworkCard";
import { MediaImage } from "@/components/ui/MediaImage";
import { PageHeader } from "@/components/ui/PageHeader";
import { Section, SectionTitle } from "@/components/ui/Section";
import { api } from "@/lib/api";
import { cn, formatDate } from "@/lib/utils";

export const metadata: Metadata = {
  title: "Réalisations",
  description: "Films, photos, mixages, créations graphiques et spectacles : les réalisations des étudiants de l'EMSI.",
};

type Props = { searchParams: Promise<{ univers?: string }> };

export default async function RealisationsPage({ searchParams }: Props) {
  const { univers } = await searchParams;
  const [universes, artworks] = await Promise.all([
    api.rooms(),
    api.artworks(`perPage=24${univers ? `&room=${encodeURIComponent(univers)}` : ""}`),
  ]);
  const active = universes.find((u) => u.slug === univers);

  return (
    <>
      <PageHeader eyebrow="Réalisations" title="Faites par nos étudiants" text="Chaque réalisation raconte un métier : derrière un mixage, un plan, une affiche ou une lumière de scène, il y a une formation de l'EMSI." accent={active?.accentColor} />

      <Section className="pt-0 sm:pt-0">
        <nav aria-label="Filtrer par univers" className="mb-12">
          <ul className="flex flex-wrap gap-2">
            <li>
              <Link href="/emsi/realisations" aria-current={!active ? "page" : undefined} className={cn("inline-flex min-h-11 items-center rounded-full border px-5 text-sm transition", !active ? "border-brand bg-brand text-on-accent" : "border-line hover:border-ink/40")}>Tout</Link>
            </li>
            {universes.filter((u) => !u.isUpcoming).map((universe) => {
              const selected = universe.slug === active?.slug;
              return (
                <li key={universe.id}>
                  <Link
                    href={`/emsi/realisations?univers=${universe.slug}`}
                    aria-current={selected ? "page" : undefined}
                    className={cn("inline-flex min-h-11 items-center gap-2 rounded-full border px-5 text-sm transition", selected ? "border-[var(--accent)] bg-[var(--accent-ink)] text-on-accent" : "border-line hover:border-[var(--accent)]")}
                    style={{ ["--accent" as string]: universe.accentColor }}
                  >
                    {!selected && <span className="size-2 rounded-full bg-[var(--accent-ink)]" aria-hidden />}
                    {universe.name}
                  </Link>
                </li>
              );
            })}
          </ul>
        </nav>
        <ArtworkGrid artworks={artworks.data} empty={active ? `Les réalisations de l'univers ${active.name} seront bientôt publiées.` : undefined} />
      </Section>
    </>
  );
}
