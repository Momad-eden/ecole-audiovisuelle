import { LocaleLink } from "@/components/i18n/LocaleLink";
import { ArrowRight } from "lucide-react";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { cn } from "@/lib/utils";
import { MediaImage } from "@/components/ui/MediaImage";
import { Section, SectionTitle } from "@/components/ui/Section";
import { InView } from "@/components/motion/InView";
import { Reveal } from "@/components/motion/Reveal";
import { ArtworkGrid } from "@/components/museum/ArtworkCard";
import { ProgramCard } from "@/components/ProgramCard";
import { NewsCard } from "@/components/NewsCard";
import { UniverseVisual } from "@/components/universe/UniverseVisual";
import { UniversesShowcase } from "@/components/universe/UniversesShowcase";
import { getDictionary } from "@/lib/i18n";
import type { BlockProps, ArtworksData, NewsData, PartnersData, ProfessionalSpaceData, ProgramsData, RoomsData } from "./types";

function SeeAll({ href, children }: { href: string; children: React.ReactNode }) {
  return (
    <LocaleLink href={href} className="group mb-12 inline-flex items-center gap-2 text-sm font-semibold text-brand">
      {children} <ArrowRight className="size-4 transition-transform group-hover:translate-x-1" aria-hidden />
    </LocaleLink>
  );
}

export function ProgramsBlock({ data, locale }: BlockProps<ProgramsData>) {
  const t = getDictionary(locale).blocks;
  const items = data.items ?? [];
  if (items.length === 0) return null;
  return (
    <Section>
      <div className="flex flex-wrap items-end justify-between gap-6">
        <SectionTitle title={data.title} />
        <SeeAll href="/emsi/formations">{t.allPrograms}</SeeAll>
      </div>
      <div className="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        {items.map((program, i) => <Reveal key={program.id} delay={(i % 3) * 100} className="h-full"><ProgramCard program={program} locale={locale} /></Reveal>)}
      </div>
    </Section>
  );
}

export function ArtworksBlock({ data, locale }: BlockProps<ArtworksData>) {
  const t = getDictionary(locale).blocks;
  const items = data.items ?? [];
  if (items.length === 0) return null;
  return (
    <Section>
      <div className="flex flex-wrap items-end justify-between gap-6">
        <SectionTitle eyebrow={t.artworksEyebrow} title={data.title} />
        <SeeAll href="/emsi/realisations">{t.allArtworks}</SeeAll>
      </div>
      <ArtworkGrid artworks={items} locale={locale} />
    </Section>
  );
}

export function RoomsBlock({ data }: BlockProps<RoomsData>) {
  const universes = data.items ?? [];
  if (universes.length === 0) return null;
  return <UniversesShowcase universes={universes} eyebrow={data.eyebrow} title={data.title} text={data.text} />;
}

export function NewsBlock({ data, locale }: BlockProps<NewsData>) {
  const t = getDictionary(locale).blocks;
  const items = data.items ?? [];
  if (items.length === 0) return null;
  return (
    <Section>
      <div className="flex flex-wrap items-end justify-between gap-6">
        <SectionTitle title={data.title} />
        <SeeAll href="/actualites">{t.allNews}</SeeAll>
      </div>
      <div className="grid gap-8 md:grid-cols-3">
        {items.map((news, i) => <Reveal key={news.id} delay={i * 100}><NewsCard news={news} locale={locale} /></Reveal>)}
      </div>
    </Section>
  );
}

export function PartnersBlock({ data, locale }: BlockProps<PartnersData>) {
  const t = getDictionary(locale).common;
  const items = data.items ?? [];
  if (items.length === 0) return null;
  return (
    <Section>
      <SectionTitle title={data.title} />
      {/* Mur de partenaires : grille à filets fins ; logo en gris qui prend ses couleurs au survol, sinon le nom. */}
      <ul className={cn("grid grid-cols-2 gap-px overflow-hidden rounded-3xl border border-line bg-line sm:grid-cols-3", items.length % 3 === 0 ? "" : "lg:grid-cols-4")}>
        {items.map((partner) => {
          const content = partner.logo ? (
            <span className="relative block h-16 w-full grayscale transition duration-300 group-hover:grayscale-0 group-focus-visible:grayscale-0 opacity-80 group-hover:opacity-100">
              <MediaImage image={partner.logo} sizes="240px" fit="contain" />
            </span>
          ) : (
            <span className="block text-balance text-center text-[0.95rem] font-semibold leading-snug text-ink/80 transition group-hover:text-ink sm:text-base">{partner.name}</span>
          );
          const cell = "group flex w-full min-h-36 items-center justify-center bg-night p-6 transition hover:bg-night-2 sm:p-8";
          return (
            <li key={partner.name} className="flex">
              {partner.website ? (
                <a href={partner.website} target="_blank" rel="noopener noreferrer" className={cell} aria-label={partner.logo ? partner.name : undefined}>
                  {content}<span className="sr-only">{t.newTab}</span>
                </a>
              ) : (
                <div className={cell} title={partner.name}>{content}{partner.logo && <span className="sr-only">{partner.name}</span>}</div>
              )}
            </li>
          );
        })}
        {/* Cases vides pour finir la dernière rangée (sinon le fond des filets apparaît). */}
        {[
          { cols: 2, className: "sm:hidden" },
          { cols: 3, className: cn("max-sm:hidden", items.length % 3 !== 0 && "lg:hidden") },
          ...(items.length % 3 === 0 ? [] : [{ cols: 4, className: "max-lg:hidden" }]),
        ].flatMap(({ cols, className }) =>
          Array.from({ length: (cols - (items.length % cols)) % cols }, (_, i) => <li key={`vide-${cols}-${i}`} className={cn("bg-night", className)} aria-hidden />),
        )}
      </ul>
    </Section>
  );
}

/** Point d'entrée unique vers l'Espace Professionnels. */
export function ProfessionalSpaceBlock({ data, locale }: BlockProps<ProfessionalSpaceData>) {
  const t = getDictionary(locale).blocks;
  return (
    <Section>
      <Reveal className="relative grid overflow-hidden rounded-[2rem] border border-line bg-night-2 lg:grid-cols-[1.2fr_1fr]" >
        <div className="relative z-10 p-8 sm:p-12" style={{ ["--accent" as string]: "var(--color-hmi)" }}>
          <p className="cartel flex items-center gap-3 text-[var(--accent-ink)]"><span className="h-px w-10 bg-[var(--accent-ink)]" aria-hidden />{t.professionalSpace}</p>
          <h2 className="display mt-5 text-[clamp(2rem,4vw,3.4rem)] text-balance">{data.title}</h2>
          {data.text && <p className="mt-5 max-w-xl text-lg text-ink/80">{data.text}</p>}
          <div className="mt-9"><ButtonLink href="/emsi/professionnels">{data.buttonLabel || t.discoverProgram}</ButtonLink></div>
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
