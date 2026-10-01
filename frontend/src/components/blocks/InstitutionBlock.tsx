import { ArrowRight } from "lucide-react";
import { Reveal } from "@/components/motion/Reveal";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { Emphasis } from "@/components/ui/Emphasis";
import { MediaImage } from "@/components/ui/MediaImage";
import { getDictionary } from "@/lib/i18n";
import type { Locale } from "@/lib/i18n/locales";
import { cn, frenchSpacing } from "@/lib/utils";
import type { InstitutionData } from "./types";

/**
 * « Présentation institutionnelle » : ce que la plateforme porte (titre, présentation), ses piliers numérotés
 * (mission, vision, valeurs…) en colonnes à filets fins, et le mot du fondateur en grande citation.
 * Ton sobre et posé, pour les partenaires, mécènes et institutions.
 */
export function InstitutionBlock({ data, locale }: { data: InstitutionData; locale: Locale }) {
  const t = getDictionary(locale).institution;
  const pillars = data.pillars ?? [];

  return (
    <section data-testid="institution" className="relative isolate border-y border-line bg-night-2 py-24 sm:py-32">
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <Reveal className="grid gap-10 lg:grid-cols-[1.4fr_1fr] lg:items-end">
          <div>
            {data.eyebrow && <p className="cartel mb-5 flex items-center gap-3 text-[var(--accent-ink)]"><span className="h-px w-10 bg-[var(--accent-ink)]" aria-hidden />{data.eyebrow}</p>}
            <h2 className="display text-balance text-[clamp(2.2rem,4.4vw,3.6rem)]"><Emphasis text={data.title} /></h2>
          </div>
          <div>
            {data.text && <p className="text-lg text-ink/80">{frenchSpacing(data.text)}</p>}
            {data.buttonLabel && data.buttonUrl && <div className="mt-7"><ButtonLink href={data.buttonUrl} variant="secondary">{data.buttonLabel}</ButtonLink></div>}
          </div>
        </Reveal>

        {pillars.length > 0 && (
          <ol className={cn("mt-16 grid border-t border-line sm:mt-20", pillars.length === 2 && "md:grid-cols-2", pillars.length === 3 && "md:grid-cols-3", pillars.length >= 4 && "md:grid-cols-2 lg:grid-cols-4")}>
            {pillars.map((pillar, index) => (
              <Reveal as="li" key={pillar.title} delay={index * 100} className={cn("py-10 md:px-8 md:first:pl-0", index > 0 && "border-t border-line md:border-l md:border-t-0")}>
                <span className="title-accent text-4xl" aria-hidden>{String(index + 1).padStart(2, "0")}</span>
                <h3 className="display mt-5 text-2xl">{frenchSpacing(pillar.title)}</h3>
                <p className="mt-3 text-ink/75">{frenchSpacing(pillar.text)}</p>
              </Reveal>
            ))}
          </ol>
        )}

        {data.quote && (
          <Reveal as="figure" className="mt-12 grid gap-8 rounded-[2rem] border border-line bg-night p-8 sm:mt-16 sm:p-12 lg:grid-cols-[auto_1fr] lg:items-center lg:gap-12">
            <div className="relative size-28 shrink-0 overflow-hidden rounded-full border border-line bg-night-3 sm:size-36">
              {data.photo ? (
                <MediaImage image={data.photo} sizes="144px" />
              ) : (
                <span className="title-accent grid size-full place-items-center text-5xl" aria-hidden>
                  {(data.author ?? "").split(/\s+/).map((part) => part[0]).join("").slice(0, 2)}
                </span>
              )}
            </div>
            <div>
              <p className="cartel text-[var(--accent-ink)]">{t.founderWord}</p>
              <blockquote className="mt-4 font-serif text-[clamp(1.5rem,2.6vw,2.2rem)] italic leading-snug text-ink">
                {locale === "fr" ? <>«&nbsp;{frenchSpacing(data.quote)}&nbsp;»</> : <>“{data.quote}”</>}
              </blockquote>
              {data.author && (
                <figcaption className="mt-6 flex items-center gap-3 text-sm">
                  <ArrowRight className="size-4 text-[var(--accent-ink)]" aria-hidden />
                  <span className="font-semibold text-ink">{data.author}</span>
                  {data.role && <span className="text-ink-muted">· {data.role}</span>}
                </figcaption>
              )}
            </div>
          </Reveal>
        )}
      </div>
    </section>
  );
}
