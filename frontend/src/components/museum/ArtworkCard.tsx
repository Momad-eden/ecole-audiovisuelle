import Link from "next/link";
import { AudioLines, Film } from "lucide-react";
import type { ArtworkSummary } from "@/lib/types";
import { MediaImage } from "@/components/ui/MediaImage";

export function ArtworkCard({ artwork }: { artwork: ArtworkSummary }) {
  const accent = artwork.room?.accentColor ?? "var(--color-brand)";

  return (
    <article className="group relative" style={{ ["--accent" as string]: accent }}>
      <Link href={`/realisations/${artwork.slug}`} className="block">
        <div className="relative aspect-[4/5] overflow-hidden rounded-2xl border border-line bg-night-3">
          {artwork.cover ? (
            <MediaImage image={artwork.cover} sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw" className="transition duration-700 group-hover:scale-[1.03]" fallbackAlt={artwork.title} />
          ) : (
            <div className="absolute inset-0 flex items-end p-6" style={{ background: "radial-gradient(90% 70% at 30% 20%, color-mix(in oklab, var(--accent) 40%, transparent), transparent 70%)" }} aria-hidden>
              <span className="display text-4xl text-ink/15">{artwork.title}</span>
            </div>
          )}
          <div className="pointer-events-none absolute inset-0 bg-gradient-to-t from-night via-night/10 to-transparent" />
          <div className="pointer-events-none absolute inset-0 opacity-0 transition duration-500 group-hover:opacity-100" style={{ background: "radial-gradient(70% 60% at 50% 0%, color-mix(in oklab, var(--accent) 35%, transparent), transparent 70%)" }} />
          <div className="absolute left-4 top-4 flex gap-2">
            {artwork.hasAudio && <span className="grid size-8 place-items-center rounded-full bg-night/70 text-[var(--accent)]" title="Contient du son"><AudioLines className="size-4" aria-hidden /><span className="sr-only">Son</span></span>}
            {artwork.hasVideo && <span className="grid size-8 place-items-center rounded-full bg-night/70 text-[var(--accent)]" title="Contient une vidéo"><Film className="size-4" aria-hidden /><span className="sr-only">Vidéo</span></span>}
          </div>
        </div>
        <div className="mt-4">
          <p className="cartel">{[artwork.room?.name, artwork.kindLabel, artwork.year].filter(Boolean).join(" · ")}</p>
          <h3 className="display mt-2 text-xl leading-snug group-hover:text-[var(--accent)]">{artwork.title}</h3>
          {artwork.summary && <p className="mt-2 line-clamp-2 text-sm text-ink-muted">{artwork.summary}</p>}
        </div>
      </Link>
    </article>
  );
}

export function ArtworkGrid({ artworks, empty }: { artworks: ArtworkSummary[]; empty?: string }) {
  if (artworks.length === 0) {
    return <p className="rounded-2xl border border-dashed border-line p-10 text-center text-ink-muted">{empty ?? "Les premières réalisations des étudiants seront bientôt publiées."}</p>;
  }
  return (
    <div className="grid gap-x-6 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
      {artworks.map((artwork) => <ArtworkCard key={artwork.id} artwork={artwork} />)}
    </div>
  );
}
