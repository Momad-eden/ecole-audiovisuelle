import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { ArtworkGrid } from "@/components/museum/ArtworkCard";
import { MediaImage } from "@/components/ui/MediaImage";
import { RichText } from "@/components/ui/RichText";
import { Section } from "@/components/ui/Section";
import { api } from "@/lib/api";
import { formatDate } from "@/lib/utils";

type Props = { params: Promise<{ slug: string }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const exhibition = await api.exhibition((await params).slug);
  return exhibition ? { title: exhibition.title, description: exhibition.subtitle ?? undefined } : {};
}

export default async function ExhibitionPage({ params }: Props) {
  const exhibition = await api.exhibition((await params).slug);
  if (!exhibition) notFound();

  return (
    <>
      <section className="relative isolate overflow-hidden pb-16 pt-40">
        <div className="absolute inset-0 -z-10">
          <MediaImage image={exhibition.cover} sizes="100vw" priority className="opacity-35" />
          <div className="absolute inset-0 bg-gradient-to-b from-night/30 to-night" />
        </div>
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <p className="cartel">{[formatDate(exhibition.startsOn), formatDate(exhibition.endsOn)].filter(Boolean).join(" – ")}{exhibition.venue && ` · ${exhibition.venue}`}</p>
          <h1 className="mt-4 max-w-4xl font-display text-5xl font-medium text-balance sm:text-7xl">{exhibition.title}</h1>
          {exhibition.subtitle && <p className="mt-4 max-w-2xl text-xl text-ink/80">{exhibition.subtitle}</p>}
        </div>
      </section>
      {exhibition.curatorialText && <Section><RichText html={exhibition.curatorialText} className="mx-auto max-w-3xl text-lg" /></Section>}
      <Section><ArtworkGrid artworks={exhibition.artworks ?? []} empty="Les œuvres de cette exposition seront bientôt en ligne." /></Section>
    </>
  );
}
