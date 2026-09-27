import Link from "next/link";
import { ArrowRight } from "lucide-react";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { MediaImage } from "@/components/ui/MediaImage";
import { Section, SectionTitle } from "@/components/ui/Section";
import { ArtworkGrid } from "@/components/museum/ArtworkCard";
import { RoomDoor } from "@/components/museum/RoomDoor";
import { ProgramCard } from "@/components/ProgramCard";
import { NewsCard } from "@/components/NewsCard";
import type { ArtworksData, NewsData, PartnersData, ProfessionalSpaceData, ProgramsData, RoomsData } from "./types";

export function ProgramsBlock({ data }: { data: ProgramsData }) {
  const items = data.items ?? [];
  if (items.length === 0) return null;
  return (
    <Section>
      <SectionTitle title={data.title} />
      <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        {items.map((program) => <ProgramCard key={program.id} program={program} />)}
      </div>
    </Section>
  );
}

export function ArtworksBlock({ data }: { data: ArtworksData }) {
  return (
    <Section>
      <div className="flex flex-wrap items-end justify-between gap-6">
        <SectionTitle title={data.title} />
        <Link href="/musee" className="mb-10 inline-flex items-center gap-2 text-sm font-medium text-amber">Tout le musée <ArrowRight className="size-4" aria-hidden /></Link>
      </div>
      <ArtworkGrid artworks={data.items ?? []} />
    </Section>
  );
}

export function RoomsBlock({ data }: { data: RoomsData }) {
  const rooms = data.items ?? [];
  if (rooms.length === 0) return null;
  return (
    <Section>
      <SectionTitle title={data.title} text={data.text} />
      <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        {rooms.map((room) => <RoomDoor key={room.id} room={room} />)}
      </div>
    </Section>
  );
}

export function NewsBlock({ data }: { data: NewsData }) {
  const items = data.items ?? [];
  if (items.length === 0) return null;
  return (
    <Section>
      <div className="flex flex-wrap items-end justify-between gap-6">
        <SectionTitle title={data.title} />
        <Link href="/actualites" className="mb-10 inline-flex items-center gap-2 text-sm font-medium text-amber">Toutes les actualités <ArrowRight className="size-4" aria-hidden /></Link>
      </div>
      <div className="grid gap-8 md:grid-cols-3">
        {items.map((news) => <NewsCard key={news.id} news={news} />)}
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
      <ul className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {items.map((partner) => {
          const content = (
            <>
              {partner.logo ? (
                <span className="relative block h-16 w-full"><MediaImage image={partner.logo} sizes="200px" className="object-contain" /></span>
              ) : null}
              <span className="block text-sm font-medium">{partner.name}</span>
            </>
          );
          return (
            <li key={partner.name} className="rounded-2xl border border-line bg-night-2 p-6">
              {partner.website ? <a href={partner.website} target="_blank" rel="noopener noreferrer" className="flex flex-col gap-4 hover:text-amber">{content}</a> : <div className="flex flex-col gap-4">{content}</div>}
            </li>
          );
        })}
      </ul>
    </Section>
  );
}

export function ProfessionalSpaceBlock({ data }: { data: ProfessionalSpaceData }) {
  return (
    <Section>
      <div className="grid overflow-hidden rounded-[2rem] border border-line bg-night-2 lg:grid-cols-2" style={{ ["--accent" as string]: "var(--color-hmi)" }}>
        <div className="beam p-8 sm:p-12">
          <p className="cartel" style={{ color: "var(--accent)" }}>Espace Professionnels</p>
          <h2 className="mt-3 font-display text-4xl text-balance">{data.title}</h2>
          {data.text && <p className="mt-4 text-lg text-ink/80">{data.text}</p>}
          <div className="mt-8"><ButtonLink href="/professionnels">{data.buttonLabel || "Découvrir le programme"}</ButtonLink></div>
        </div>
        {data.image ? (
          <div className="relative min-h-72"><MediaImage image={data.image} sizes="(min-width: 1024px) 50vw, 100vw" /></div>
        ) : (
          <div className="hidden min-h-72 lg:block" style={{ background: "radial-gradient(80% 80% at 70% 30%, color-mix(in oklab, var(--accent) 30%, transparent), transparent 70%)" }} aria-hidden />
        )}
      </div>
    </Section>
  );
}
