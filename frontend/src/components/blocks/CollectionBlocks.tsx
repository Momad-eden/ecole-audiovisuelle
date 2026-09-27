import Link from "next/link";
import { ArrowRight } from "lucide-react";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { MediaImage } from "@/components/ui/MediaImage";
import { Section, SectionTitle } from "@/components/ui/Section";
import { InView } from "@/components/motion/InView";
import { Reveal } from "@/components/motion/Reveal";
import { ArtworkGrid } from "@/components/museum/ArtworkCard";
import { ProgramCard } from "@/components/ProgramCard";
import { NewsCard } from "@/components/NewsCard";
import { UniverseVisual } from "@/components/universe/UniverseVisual";
import { UniversesShowcase } from "@/components/universe/UniversesShowcase";
import type { ArtworksData, NewsData, PartnersData, ProfessionalSpaceData, ProgramsData, RoomsData } from "./types";

function SeeAll({ href, children }: { href: string; children: React.ReactNode }) {
  return (
    <Link href={href} className="group mb-12 inline-flex items-center gap-2 text-sm font-semibold text-brand">
      {children} <ArrowRight className="size-4 transition-transform group-hover:translate-x-1" aria-hidden />
    </Link>
  );
}

export function ProgramsBlock({ data }: { data: ProgramsData }) {
  const items = data.items ?? [];
  if (items.length === 0) return null;
  return (
    <Section>
      <div className="flex flex-wrap items-end justify-between gap-6">
        <SectionTitle title={data.title} />
        <SeeAll href="/formations">Toutes les formations</SeeAll>
      </div>
      <div className="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        {items.map((program, i) => <Reveal key={program.id} delay={(i % 3) * 100} className="h-full"><ProgramCard program={program} /></Reveal>)}
      </div>
    </Section>
  );
}

export function ArtworksBlock({ data }: { data: ArtworksData }) {
  const items = data.items ?? [];
  if (items.length === 0) return null;
  return (
    <Section>
      <div className="flex flex-wrap items-end justify-between gap-6">
        <SectionTitle eyebrow="Faits par nos étudiants" title={data.title} />
        <SeeAll href="/realisations">Toutes les réalisations</SeeAll>
      </div>
      <ArtworkGrid artworks={items} />
    </Section>
  );
}

export function RoomsBlock({ data }: { data: RoomsData }) {
  const universes = data.items ?? [];
  if (universes.length === 0) return null;
  return <UniversesShowcase universes={universes} eyebrow={data.eyebrow} title={data.title} text={data.text} />;
}

export function NewsBlock({ data }: { data: NewsData }) {
  const items = data.items ?? [];
  if (items.length === 0) return null;
  return (
    <Section>
      <div className="flex flex-wrap items-end justify-between gap-6">
        <SectionTitle title={data.title} />
        <SeeAll href="/actualites">Toutes les actualités</SeeAll>
      </div>
      <div className="grid gap-8 md:grid-cols-3">
        {items.map((news, i) => <Reveal key={news.id} delay={i * 100}><NewsCard news={news} /></Reveal>)}
      </div>
    </Section>
  );
}

export function PartnersBlock({ data }: { data: PartnersData }) {
  const items = data.items ?? [];
  if (items.length === 0) return null;
  return (
    <Section>
      <SectionTitle title={data.title} />
      <ul className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        {items.map((partner) => {
          const content = (
            <>
              {partner.logo && <span className="relative block h-14 w-full"><MediaImage image={partner.logo} sizes="200px" fit="contain" className="object-left" /></span>}
              <span className="block font-medium leading-snug">{partner.name}</span>
            </>
          );
          return (
            <li key={partner.name} className="rounded-3xl border border-line bg-night-2 p-7 transition hover:border-ink/30">
              {partner.website ? (
                <a href={partner.website} target="_blank" rel="noopener noreferrer" className="flex h-full flex-col gap-4 transition hover:text-brand">{content}<span className="sr-only">(nouvel onglet)</span></a>
              ) : (
                <div className="flex h-full flex-col gap-4">{content}</div>
              )}
            </li>
          );
        })}
      </ul>
    </Section>
  );
}

/** Point d'entrée unique vers l'Espace Professionnels. */
export function ProfessionalSpaceBlock({ data }: { data: ProfessionalSpaceData }) {
  return (
    <Section>
      <Reveal className="relative grid overflow-hidden rounded-[2rem] border border-line bg-night-2 lg:grid-cols-[1.2fr_1fr]" >
        <div className="relative z-10 p-8 sm:p-12" style={{ ["--accent" as string]: "var(--color-hmi)" }}>
          <p className="cartel flex items-center gap-3 text-[var(--accent)]"><span className="h-px w-10 bg-[var(--accent)]" aria-hidden />Espace Professionnels</p>
          <h2 className="display mt-5 text-[clamp(2rem,4vw,3.4rem)] text-balance">{data.title}</h2>
          {data.text && <p className="mt-5 max-w-xl text-lg text-ink/80">{data.text}</p>}
          <div className="mt-9"><ButtonLink href="/professionnels">{data.buttonLabel || "Découvrir le programme"}</ButtonLink></div>
        </div>
        {data.image ? (
          <div className="relative min-h-72"><MediaImage image={data.image} sizes="(min-width: 1024px) 45vw, 100vw" /></div>
        ) : (
          <div className="relative min-h-72 border-t border-line lg:border-l lg:border-t-0" style={{ ["--accent" as string]: "var(--color-gold)", background: "radial-gradient(80% 70% at 60% 30%, color-mix(in oklab, var(--color-hmi) 20%, transparent), transparent 70%)" }}>
            <InView className="absolute inset-6"><UniverseVisual kind="stage" /></InView>
          </div>
        )}
      </Reveal>
    </Section>
  );
}
