import type { Metadata } from "next";
import Link from "next/link";
import { notFound } from "next/navigation";
import { ArrowLeft, ArrowRight } from "lucide-react";
import { PlayButton } from "@/components/audio/PlayButton";
import { ArtworkWaveform } from "@/components/museum/ArtworkWaveform";
import { MediaImage } from "@/components/ui/MediaImage";
import { RichText } from "@/components/ui/RichText";
import { VideoEmbed } from "@/components/ui/VideoEmbed";
import { api } from "@/lib/api";
import { formatDuration, siteUrl } from "@/lib/utils";

type Props = { params: Promise<{ slug: string }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const artwork = await api.artwork((await params).slug);
  if (!artwork) return {};
  return {
    title: artwork.title,
    description: artwork.summary ?? undefined,
    openGraph: { title: artwork.title, description: artwork.summary ?? undefined, images: artwork.cover ? [{ url: artwork.cover.url, alt: artwork.cover.alt }] : undefined },
  };
}

export default async function ArtworkPage({ params }: Props) {
  const artwork = await api.artwork((await params).slug);
  if (!artwork) notFound();

  const learnHref = artwork.room ? `/univers/${artwork.room.slug}#filieres` : "/formations";
  const accent = artwork.room?.accentColor ?? "var(--color-brand)";
  const href = `/realisations/${artwork.slug}`;
  const track = artwork.audio ? { src: artwork.audio.url, title: artwork.title, subtitle: artwork.room?.name, href, peaks: artwork.audio.peaks, accent } : null;

  const jsonLd = {
    "@context": "https://schema.org",
    "@type": artwork.audio ? "AudioObject" : artwork.videoUrl ? "VideoObject" : "CreativeWork",
    name: artwork.title,
    description: artwork.summary ?? undefined,
    url: `${siteUrl}${href}`,
    image: artwork.cover?.url,
    dateCreated: artwork.year ? String(artwork.year) : undefined,
    creator: artwork.credits?.map((c) => ({ "@type": "Person", name: c.name, jobTitle: c.role })),
    ...(artwork.audio ? { contentUrl: artwork.audio.url } : {}),
  };

  return (
    <article style={{ ["--accent" as string]: accent }}>
      <div className="mx-auto max-w-7xl px-4 pt-32 sm:px-6 lg:px-8">
        <Link href={artwork.room ? `/realisations?univers=${artwork.room.slug}` : "/realisations"} className="cartel inline-flex items-center gap-2 hover:text-ink">
          <ArrowLeft className="size-4" aria-hidden /> Réalisations{artwork.room ? ` · ${artwork.room.name}` : ""}
        </Link>
      </div>

      <div className="mx-auto grid max-w-7xl gap-12 px-4 py-10 sm:px-6 lg:grid-cols-12 lg:px-8">
        <div className="space-y-6 lg:col-span-7">
          {artwork.videoUrl ? (
            <VideoEmbed url={artwork.videoUrl} title={artwork.title} poster={artwork.cover?.url} />
          ) : (
            artwork.cover && (
              <div className="relative aspect-[4/3] overflow-hidden rounded-3xl border border-line bg-night-3 shadow-[0_0_120px_-40px_var(--accent)]">
                <MediaImage image={artwork.cover} sizes="(min-width: 1024px) 58vw, 100vw" priority fallbackAlt={artwork.title} />
              </div>
            )
          )}

          {track && (
            <div className="rounded-3xl border border-line bg-night-2 p-6">
              <div className="flex flex-wrap items-center gap-4">
                <PlayButton track={track} />
                {artwork.audio?.durationSeconds && <span className="cartel">Durée {formatDuration(artwork.audio.durationSeconds)}</span>}
              </div>
              <ArtworkWaveform track={track} />
              {artwork.transcript && (
                <details className="mt-4 text-sm text-ink-muted">
                  <summary className="cursor-pointer text-ink">Description du son (accessibilité)</summary>
                  <p className="mt-3 whitespace-pre-line">{artwork.transcript}</p>
                </details>
              )}
            </div>
          )}

          {artwork.gallery.length > 0 && (
            <ul className="grid grid-cols-2 gap-4 sm:grid-cols-3">
              {artwork.gallery.map((image, index) => (
                <li key={image.url} className="relative aspect-square overflow-hidden rounded-2xl border border-line">
                  <a href={image.url} target="_blank" rel="noopener noreferrer">
                    <MediaImage image={{ ...image, alt: `${artwork.title} — image ${index + 1}` }} sizes="(min-width: 1024px) 20vw, 50vw" />
                  </a>
                </li>
              ))}
            </ul>
          )}
        </div>

        <div className="lg:col-span-5">
          <p className="cartel" style={{ color: "var(--accent-ink)" }}>{[artwork.kindLabel, artwork.year].filter(Boolean).join(" · ")}</p>
          <h1 className="display mt-4 text-[clamp(2.2rem,4.5vw,3.8rem)] text-balance">{artwork.title}</h1>
          {artwork.summary && <p className="mt-5 text-lg text-ink/85">{artwork.summary}</p>}

          <dl className="mt-10 divide-y divide-line border-y border-line text-sm">
            {artwork.room && <div className="flex justify-between gap-6 py-3"><dt className="text-ink-muted">Univers</dt><dd>{artwork.room.name}</dd></div>}
            {artwork.track && <div className="flex justify-between gap-6 py-3"><dt className="text-ink-muted">Filière</dt><dd>{artwork.track.name}</dd></div>}
            {artwork.cohort && <div className="flex justify-between gap-6 py-3"><dt className="text-ink-muted">Promotion</dt><dd>{artwork.cohort}</dd></div>}
            {artwork.credits?.map((credit) => (
              <div key={credit.name + credit.role} className="flex justify-between gap-6 py-3"><dt className="text-ink-muted">{credit.role}</dt><dd className="text-right">{credit.name}</dd></div>
            ))}
          </dl>

          {artwork.equipment.length > 0 && (
            <div className="mt-8">
              <h2 className="cartel mb-3">Matériel utilisé</h2>
              <ul className="flex flex-wrap gap-2">
                {artwork.equipment.map((item) => <li key={item} className="rounded-full border border-line px-3 py-1 text-sm">{item}</li>)}
              </ul>
            </div>
          )}

          {artwork.creationStory && (
            <div className="mt-10">
              <h2 className="display mb-4 text-2xl">Récit de création</h2>
              <RichText html={artwork.creationStory} />
            </div>
          )}

          {artwork.track && (
            <Link href={learnHref} className="mt-12 flex items-center justify-between gap-4 rounded-3xl border border-line bg-night-2 p-6 transition hover:border-[var(--accent)]">
              <span>
                <span className="cartel block">Apprendre à faire ça</span>
                <span className="display mt-1 block text-xl">Filière {artwork.track.name}</span>
              </span>
              <ArrowRight className="size-5 text-[var(--accent-ink)]" aria-hidden />
            </Link>
          )}
        </div>
      </div>
      <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd).replace(/</g, "\\u003c") }} />
    </article>
  );
}
