import type { Metadata } from "next";
import Link from "next/link";
import { ArtworkGrid } from "@/components/museum/ArtworkCard";
import { RoomDoor } from "@/components/museum/RoomDoor";
import { Section, SectionTitle } from "@/components/ui/Section";
import { MediaImage } from "@/components/ui/MediaImage";
import { api } from "@/lib/api";
import { formatDate } from "@/lib/utils";

export const metadata: Metadata = {
  title: "Le musée",
  description: "Les salles du musée numérique de l'EMSI : son, lumière, image et visuel. Réalisations des apprenants et de l'école.",
};

export default async function MuseumPage() {
  const [rooms, artworks, exhibitions] = await Promise.all([api.rooms(), api.artworks("perPage=12"), api.exhibitions()]);
  const current = exhibitions.filter((e) => e.state !== "past");

  return (
    <>
      <section className="beam relative pb-8 pt-24">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <p className="cartel mb-4">Musée numérique</p>
          <h1 className="max-w-4xl font-display text-5xl leading-tight font-medium text-balance sm:text-7xl">Plan du musée</h1>
          <p className="mt-6 max-w-2xl text-lg text-ink/80">Chaque salle rassemble les œuvres d&apos;une discipline. Entrez, écoutez, regardez : derrière chaque œuvre, un métier et une formation.</p>
        </div>
      </section>

      <Section>
        <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
          {rooms.map((room) => <RoomDoor key={room.id} room={room} />)}
        </div>
      </Section>

      {current.length > 0 && (
        <Section>
          <SectionTitle eyebrow="Expositions" title="À voir en ce moment" />
          <ul className="grid gap-6 md:grid-cols-2">
            {current.map((exhibition) => (
              <li key={exhibition.id}>
                <Link href={`/expositions/${exhibition.slug}`} className="group relative flex min-h-72 flex-col justify-end overflow-hidden rounded-3xl border border-line p-8">
                  <MediaImage image={exhibition.cover} sizes="(min-width: 768px) 50vw, 100vw" className="opacity-50 transition duration-700 group-hover:scale-105" />
                  <div className="absolute inset-0 bg-gradient-to-t from-night to-transparent" />
                  <div className="relative">
                    <p className="cartel">{exhibition.state === "upcoming" ? "À venir" : "En cours"}{exhibition.startsOn && ` · ${formatDate(exhibition.startsOn)}`}</p>
                    <h3 className="mt-2 font-display text-3xl">{exhibition.title}</h3>
                    {exhibition.subtitle && <p className="mt-2 text-ink/80">{exhibition.subtitle}</p>}
                  </div>
                </Link>
              </li>
            ))}
          </ul>
        </Section>
      )}

      <Section>
        <SectionTitle eyebrow="Collection" title="Dernières œuvres" />
        <ArtworkGrid artworks={artworks.data} />
      </Section>
    </>
  );
}
