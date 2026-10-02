import { LocaleLink } from "@/components/i18n/LocaleLink";
import { AudioLines, Film } from "lucide-react";
import type { ArtworkSummary } from "@/lib/types";
import { MediaImage } from "@/components/ui/MediaImage";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { EmptyState } from "@/components/ui/EmptyState";
import { getDictionary } from "@/lib/i18n";
import type { Locale } from "@/lib/i18n/locales";

export function ArtworkCard({ artwork, locale }: { artwork: ArtworkSummary; locale: Locale }) {
  const t = getDictionary(locale).cards;
  const accent = artwork.room?.accentColor ?? "var(--color-brand)";

  return (
    <article className="group relative" style={{ ["--accent" as string]: accent }}>
      <LocaleLink href={`/emsi/realisations/${artwork.slug}`} className="block">
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
            {artwork.hasAudio && <span className="grid size-8 place-items-center rounded-full bg-night/70 text-[var(--accent-ink)]" title={t.hasAudio}><AudioLines className="size-4" aria-hidden /><span className="sr-only">{t.audio}</span></span>}
            {artwork.hasVideo && <span className="grid size-8 place-items-center rounded-full bg-night/70 text-[var(--accent-ink)]" title={t.hasVideo}><Film className="size-4" aria-hidden /><span className="sr-only">{t.video}</span></span>}
          </div>
        </div>
        <div className="mt-4">
          <p className="cartel">{[artwork.room?.name, artwork.kindLabel, artwork.year].filter(Boolean).join(" · ")}</p>
          <h3 className="display mt-2 text-xl leading-snug group-hover:text-[var(--accent-ink)]">{artwork.title}</h3>
          {artwork.summary && <p className="mt-2 line-clamp-2 text-sm text-ink-muted">{artwork.summary}</p>}
        </div>
      </LocaleLink>
    </article>
  );
}

export function ArtworkGrid({ artworks, empty, locale }: { artworks: ArtworkSummary[]; empty?: string; locale: Locale }) {
  const { cards: t, common } = getDictionary(locale);
  if (artworks.length === 0) {
    return (
      <EmptyState title={empty ?? t.artworksEmptyTitle} text={t.artworksEmptyText} visual="image">
        <ButtonLink href="/emsi">{common.discoverUniverses}</ButtonLink>
      </EmptyState>
    );
  }
  return (
    <div className="grid gap-x-6 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
      {artworks.map((artwork) => <ArtworkCard key={artwork.id} artwork={artwork} locale={locale} />)}
    </div>
  );
}
