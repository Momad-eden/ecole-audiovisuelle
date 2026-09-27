import type { Metadata } from "next";
import Link from "next/link";
import { notFound } from "next/navigation";
import { ArrowLeft } from "lucide-react";
import { ArtworkGrid } from "@/components/museum/ArtworkCard";
import { MediaImage } from "@/components/ui/MediaImage";
import { Section } from "@/components/ui/Section";
import { api } from "@/lib/api";

type Props = { params: Promise<{ salle: string }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const room = await api.room((await params).salle);
  return room ? { title: room.name, description: room.tagline ?? undefined } : {};
}

export default async function RoomPage({ params }: Props) {
  const room = await api.room((await params).salle);
  if (!room) notFound();

  return (
    <div style={{ ["--accent" as string]: room.accentColor }}>
      <section className="relative isolate overflow-hidden pb-16 pt-24">
        <div className="absolute inset-0 -z-10">
          <MediaImage image={room.cover} sizes="100vw" priority className="opacity-30" />
          <div className="absolute inset-0 bg-gradient-to-b from-night/40 to-night" />
        </div>
        <div className="beam absolute inset-0 -z-10" aria-hidden />
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <Link href="/musee" className="cartel inline-flex items-center gap-2 hover:text-ink"><ArrowLeft className="size-4" aria-hidden /> Plan du musée</Link>
          <h1 className="mt-6 font-display text-5xl font-medium sm:text-7xl">{room.name}</h1>
          {room.tagline && <p className="mt-4 max-w-2xl text-xl text-[var(--accent)]">{room.tagline}</p>}
          {room.intro && <p className="mt-6 max-w-3xl whitespace-pre-line text-lg text-ink/80">{room.intro}</p>}
        </div>
      </section>
      <Section>
        <ArtworkGrid artworks={room.artworks ?? []} empty="Cette salle attend ses premières œuvres." />
      </Section>
    </div>
  );
}
