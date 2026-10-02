import { Check, MapPin, Phone } from "lucide-react";
import { InView } from "@/components/motion/InView";
import { Reveal } from "@/components/motion/Reveal";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { MediaImage } from "@/components/ui/MediaImage";
import { Section, SectionTitle } from "@/components/ui/Section";
import { UniverseVisual } from "@/components/universe/UniverseVisual";
import { getDictionary } from "@/lib/i18n";
import type { Place, UniverseVisualKind } from "@/lib/types";
import type { BlockProps } from "./types";

/** Une lumière et une signature par campus, dans l'ordre de l'admin. */
const LOOKS: { accent: string; visual: UniverseVisualKind }[] = [
  { accent: "var(--color-brand)", visual: "stage" },
  { accent: "var(--color-hmi)", visual: "sound" },
  { accent: "var(--color-violet)", visual: "image" },
];

/** Les campus de l'EMSI côte à côte : mêmes formations, chacun avec son lieu et ses atouts. */
export function CampusesBlock({ data, locale }: BlockProps<{ eyebrow?: string; title?: string; text?: string; items?: Place[] }>) {
  const { campus: t, common } = getDictionary(locale);
  const campuses = data.items ?? [];
  if (campuses.length === 0) return null;

  return (
    <Section>
      <SectionTitle eyebrow={data.eyebrow} title={data.title} text={data.text} />
      <div className={campuses.length > 1 ? "grid gap-5 lg:grid-cols-2" : "max-w-3xl"}>
        {campuses.map((campus, index) => {
          const look = LOOKS[index % LOOKS.length];
          const phone = campus.phone?.replace(/[^0-9+]/g, "");
          return (
            <Reveal key={campus.id} delay={index * 120} className="h-full">
              <article className="group flex h-full flex-col overflow-hidden rounded-[2rem] border border-line bg-night-2" style={{ ["--accent" as string]: look.accent }}>
                <div className="relative aspect-[16/10] overflow-hidden border-b border-line" style={{ background: "radial-gradient(80% 70% at 60% 30%, color-mix(in oklab, var(--accent) 24%, transparent), transparent 70%)" }}>
                  {campus.image ? (
                    <MediaImage image={campus.image} sizes="(min-width: 1024px) 50vw, 100vw" className="transition duration-700 group-hover:scale-105" />
                  ) : (
                    <InView className="absolute inset-6"><UniverseVisual kind={look.visual} /></InView>
                  )}
                  <div className="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-night-2 to-transparent" aria-hidden />
                  <p className="display absolute bottom-4 left-6 text-[clamp(2.6rem,6vw,4.6rem)] leading-none text-ink drop-shadow-[0_2px_20px_rgb(0_0_0/0.5)]">{campus.city ?? campus.name}</p>
                </div>
                <div className="flex flex-1 flex-col p-7 sm:p-9">
                  <p className="cartel text-[var(--accent-ink)]">{campus.name}</p>
                  {campus.tagline && <p className="display mt-3 text-xl leading-snug sm:text-2xl">{campus.tagline}</p>}
                  {campus.description && <p className="mt-4 text-ink-muted">{campus.description}</p>}
                  {campus.highlights.length > 0 && (
                    <ul className="mt-6 space-y-3">
                      {campus.highlights.map((highlight) => (
                        <li key={highlight} className="flex gap-3"><Check className="mt-0.5 size-5 shrink-0 text-[var(--accent-ink)]" aria-hidden />{highlight}</li>
                      ))}
                    </ul>
                  )}
                  {(campus.address || campus.phone) && (
                    <ul className="mt-6 space-y-2 border-t border-line pt-5 text-sm text-ink/85">
                      {campus.address && <li className="flex gap-3"><MapPin className="mt-0.5 size-4 shrink-0 text-ink-muted" aria-hidden />{campus.address}</li>}
                      {campus.phone && phone && <li className="flex gap-3"><Phone className="mt-0.5 size-4 shrink-0 text-ink-muted" aria-hidden /><a href={`tel:${phone}`} className="hover:text-[var(--accent-ink)]">{campus.phone}</a></li>}
                    </ul>
                  )}
                  <div className="mt-auto flex flex-wrap gap-3 pt-8">
                    <ButtonLink href={`/candidater?campus=${campus.slug}`}>{t.apply(campus.city ?? campus.name)}</ButtonLink>
                    {campus.pageUrl && <ButtonLink href={campus.pageUrl} variant="secondary">{t.discover}</ButtonLink>}
                    {campus.mapUrl && <ButtonLink href={campus.mapUrl} variant="secondary">{common.directions}</ButtonLink>}
                  </div>
                </div>
              </article>
            </Reveal>
          );
        })}
      </div>
    </Section>
  );
}
