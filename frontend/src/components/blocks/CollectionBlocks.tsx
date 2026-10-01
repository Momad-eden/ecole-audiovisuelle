import { LocaleLink } from "@/components/i18n/LocaleLink";
import { ArrowRight } from "lucide-react";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { cn } from "@/lib/utils";
import { MediaImage } from "@/components/ui/MediaImage";
import { Container, Section, SectionTitle } from "@/components/ui/Section";
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

type Partner = NonNullable<PartnersData["items"]>[number];

/**
 * Partenaires. « Mur » : grille à filets fins, regroupée par catégorie quand il y en a plusieurs ; logo en gris
 * qui prend ses couleurs au survol, sinon le nom. « Bandeau » : une ligne discrète « Avec le soutien de »,
 * pour le haut de l'accueil.
 */
export function PartnersBlock({ data, locale }: BlockProps<PartnersData>) {
  const dictionary = getDictionary(locale);
  const items = data.items ?? [];
  if (items.length === 0) return null;

  if (data.layout === "strip") {
    return (
      <section data-testid="partners-strip" className="border-y border-line py-8">
        <Container className="flex flex-col gap-5 lg:flex-row lg:items-center lg:gap-10">
          <p className="cartel shrink-0 text-ink-muted">{data.title || dictionary.institution.supportedBy}</p>
          <ul className="grid gap-x-8 gap-y-2.5 sm:grid-cols-2 lg:flex lg:flex-wrap lg:items-center lg:gap-y-3">
            {items.map((partner) => (
              <li key={partner.name} className="flex items-center gap-8 lg:[&+li]:before:size-1 lg:[&+li]:before:rounded-full lg:[&+li]:before:bg-ink/25 lg:[&+li]:before:content-['']">
                {partner.logo ? (
                  <span className="relative block h-10 w-28 opacity-75 grayscale transition hover:opacity-100 hover:grayscale-0"><MediaImage image={partner.logo} sizes="112px" fit="contain" /></span>
                ) : (
                  <span className="text-sm font-semibold text-ink/75">{partner.name}</span>
                )}
              </li>
            ))}
          </ul>
        </Container>
      </section>
    );
  }

  const groups = Object.entries(Object.groupBy(items, (partner) => partner.group ?? "")) as [string, Partner[]][];
  const grouped = groups.length > 1;

  return (
    <Section>
      <SectionTitle title={data.title} />
      <div className="grid gap-12">
        {groups.map(([group, partners]) => (
          <div key={group}>
            {grouped && group && <h3 className="cartel mb-5 text-ink">{group}</h3>}
            <PartnerWall partners={partners} newTab={dictionary.common.newTab} />
          </div>
        ))}
      </div>
    </Section>
  );
}

function PartnerWall({ partners, newTab }: { partners: Partner[]; newTab: string }) {
  const count = partners.length;
  return (
    // Cases qui s'élargissent pour finir chaque rangée (pas de case vide, même avec un seul partenaire).
    <ul className="flex flex-wrap gap-px overflow-hidden rounded-3xl border border-line bg-line">
      {partners.map((partner) => {
        const content = partner.logo ? (
          <span className="relative block h-16 w-full opacity-80 grayscale transition duration-300 group-hover:opacity-100 group-hover:grayscale-0 group-focus-visible:grayscale-0">
            <MediaImage image={partner.logo} sizes="240px" fit="contain" />
          </span>
        ) : (
          <span className="block text-balance text-center text-[0.95rem] font-semibold leading-snug text-ink/80 transition group-hover:text-ink sm:text-base">{partner.name}</span>
        );
        const cell = "group flex w-full min-h-36 items-center justify-center bg-night p-6 transition hover:bg-night-2 sm:p-8";
        return (
          <li key={partner.name} className={cn("flex grow basis-[calc(50%-1px)] sm:basis-[calc(33.333%-1px)]", count % 3 !== 0 && "lg:basis-[calc(25%-1px)]")}>
            {partner.website ? (
              <a href={partner.website} target="_blank" rel="noopener noreferrer" className={cell} aria-label={partner.logo ? partner.name : undefined}>
                {content}<span className="sr-only">{newTab}</span>
              </a>
            ) : (
              <div className={cell} title={partner.name}>{content}{partner.logo && <span className="sr-only">{partner.name}</span>}</div>
            )}
          </li>
        );
      })}
    </ul>
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
