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
  const [universes, artworks, exhibitions] = await Promise.all([
    api.rooms(),
    api.artworks(`perPage=24${univers ? `&room=${encodeURIComponent(univers)}` : ""}`),
    api.exhibitions(),
  ]);
  const current = exhibitions.filter((e) => e.state !== "past");
  const active = universes.find((u) => u.slug === univers);

  return (
    <>
      <PageHeader eyebrow="Réalisations" title="Faites par nos étudiants" text="Chaque réalisation raconte un métier : derrière un mixage, un plan, une affiche ou une lumière de scène, il y a une formation de l'EMSI." accent={active?.accentColor} />

      <Section className="pt-0 sm:pt-0">
        <nav aria-label="Filtrer par univers" className="mb-12">
          <ul className="flex flex-wrap gap-2">
            <li>
              <Link href="/realisations" aria-current={!active ? "page" : undefined} className={cn("inline-flex min-h-11 items-center rounded-full border px-5 text-sm transition", !active ? "border-brand bg-brand text-on-accent" : "border-line hover:border-ink/40")}>Tout</Link>
            </li>
            {universes.filter((u) => !u.isUpcoming).map((universe) => {
              const selected = universe.slug === active?.slug;
              return (
                <li key={universe.id}>
                  <Link
                    href={`/realisations?univers=${universe.slug}`}
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

      {current.length > 0 && (
        <Section>
          <SectionTitle eyebrow="Expositions" title="À voir en ce moment" />
          <ul className="grid gap-6 md:grid-cols-2">
            {current.map((exhibition) => (
              <li key={exhibition.id}>
                <Link href={`/expositions/${exhibition.slug}`} className="group relative flex min-h-72 flex-col justify-end overflow-hidden rounded-[2rem] border border-line p-8">
                  <MediaImage image={exhibition.cover} sizes="(min-width: 768px) 50vw, 100vw" className="opacity-50 transition duration-700 group-hover:scale-105" />
                  <div className="absolute inset-0 bg-gradient-to-t from-night to-transparent" />
                  <div className="relative">
                    <p className="cartel">{exhibition.state === "upcoming" ? "À venir" : "En cours"}{exhibition.startsOn && ` · ${formatDate(exhibition.startsOn)}`}</p>
                    <h3 className="display mt-2 text-3xl">{exhibition.title}</h3>
                    {exhibition.subtitle && <p className="mt-2 text-ink/80">{exhibition.subtitle}</p>}
                  </div>
                </Link>
              </li>
            ))}
          </ul>
        </Section>
      )}
    </>
  );
}
