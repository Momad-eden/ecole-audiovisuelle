import { CurrentCrumb } from "@/components/layout/domain-crumb";
import type { Metadata } from "next";
import { LocaleLink } from "@/components/i18n/LocaleLink";
import { notFound } from "next/navigation";
import { ArrowLeft, ArrowRight, Check } from "lucide-react";
import { CtaBlock } from "@/components/blocks/ContentBlocks";
import { InView } from "@/components/motion/InView";
import { Reveal } from "@/components/motion/Reveal";
import { ArtworkGrid } from "@/components/museum/ArtworkCard";
import { ProgramCard } from "@/components/ProgramCard";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { PageHeader } from "@/components/ui/PageHeader";
import { Section, SectionTitle } from "@/components/ui/Section";
import { UniverseVisual } from "@/components/universe/UniverseVisual";
import { api } from "@/lib/api";
import type { Locale } from "@/lib/i18n/locales";

type Props = { params: Promise<{ locale: Locale; slug: string }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { locale, slug } = await params;
  const universe = await api.room(slug, locale);
  return universe ? { title: universe.name, description: universe.tagline ?? undefined } : {};
}

export default async function UniversePage({ params }: Props) {
  const { locale, slug } = await params;
  const [universe, all] = await Promise.all([api.room(slug, locale), api.rooms(locale)]);
  if (!universe) notFound();

  const tracks = universe.tracks ?? [];
  const programs = universe.programs ?? [];
  const artworks = universe.artworks ?? [];
  const position = all.findIndex((u) => u.slug === universe.slug);
  const next = all.length > 1 ? all[(position + 1) % all.length] : null;

  return (
    <div style={{ ["--accent" as string]: universe.accentColor }}>
      <CurrentCrumb title={universe.name} />
      <PageHeader
        eyebrow={universe.isUpcoming ? "Bientôt à l'EMSI" : `Univers ${String(position + 1).padStart(2, "0")}`}
        title={universe.name}
        text={universe.tagline}
        accent={universe.accentColor}
        aside={
          <div className="relative aspect-[4/3] overflow-hidden rounded-[2rem] border border-line bg-night-2" style={{ background: "radial-gradient(80% 70% at 60% 35%, color-mix(in oklab, var(--accent) 22%, transparent), transparent 70%)" }}>
            <InView className="absolute inset-6"><UniverseVisual kind={universe.visual} /></InView>
          </div>
        }
      >
        <LocaleLink href="/emsi" className="cartel mt-8 inline-flex items-center gap-2 hover:text-ink"><ArrowLeft className="size-4" aria-hidden /> Tous les univers</LocaleLink>
        {universe.intro && <p className="mt-8 max-w-2xl whitespace-pre-line text-ink/80">{universe.intro}</p>}
        <div className="mt-10 flex flex-wrap gap-3">
          {universe.isUpcoming ? (
            <ButtonLink href="/contact" size="lg">Être informé de l&apos;ouverture</ButtonLink>
          ) : (
            <>
              <ButtonLink href="/candidater" size="lg">Candidater</ButtonLink>
              {tracks.length > 0 && <ButtonLink href="#filieres" variant="secondary" size="lg">Voir les filières</ButtonLink>}
            </>
          )}
        </div>
      </PageHeader>

      {tracks.length > 0 && (
        <Section id="filieres">
          <SectionTitle eyebrow="Filières" title="Ce que vous apprendrez" text="Des compétences pratiques, travaillées sur du matériel professionnel, et les métiers auxquels elles préparent." />
          <div className="space-y-6">
            {tracks.map((track, index) => (
              <Reveal key={track.id} className="grid gap-10 rounded-[2rem] border border-line bg-night-2 p-7 sm:p-10 lg:grid-cols-[1fr_1.3fr]">
                <div>
                  <p className="cartel tabular-nums text-[var(--accent-ink)]">{`${String(index + 1).padStart(2, "0")} · Filière`}</p>
                  <h3 className="display mt-4 text-[clamp(1.8rem,3.4vw,2.8rem)]">{track.name}</h3>
                  {track.summary && <p className="mt-5 text-ink/80">{track.summary}</p>}
                </div>
                <div className="grid gap-8 sm:grid-cols-2">
                  {track.skills.length > 0 && (
                    <div>
                      <p className="cartel mb-4">Vous apprendrez</p>
                      <ul className="space-y-3">
                        {track.skills.map((skill) => (
                          <li key={skill} className="flex gap-3"><Check className="mt-0.5 size-4 shrink-0 text-[var(--accent-ink)]" aria-hidden />{skill}</li>
                        ))}
                      </ul>
                    </div>
                  )}
                  {track.outcomes.length > 0 && (
                    <div>
                      <p className="cartel mb-4">Métiers visés</p>
                      <ul className="flex flex-wrap gap-2">
                        {track.outcomes.map((outcome) => (
                          <li key={outcome} className="rounded-full border border-[color-mix(in_oklab,var(--accent)_45%,transparent)] px-3 py-1.5 text-sm">{outcome}</li>
                        ))}
                      </ul>
                    </div>
                  )}
                </div>
              </Reveal>
            ))}
          </div>
        </Section>
      )}

      {programs.length > 0 && (
        <Section>
          <SectionTitle eyebrow="Formations" title="Les formations de cet univers" />
          <div className="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            {programs.map((program) => <ProgramCard key={program.id} program={program} />)}
          </div>
        </Section>
      )}

      {artworks.length > 0 && (
        <Section>
          <SectionTitle eyebrow="Réalisations" title="Faites par nos étudiants" />
          <ArtworkGrid artworks={artworks} />
        </Section>
      )}

      {next && next.slug !== universe.slug && (
        <section className="border-y border-line" style={{ ["--accent" as string]: next.accentColor }}>
          <LocaleLink href={`/emsi/univers/${next.slug}`} className="group mx-auto flex max-w-7xl items-center justify-between gap-6 px-4 py-14 sm:px-6 lg:px-8">
            <span>
              <span className="cartel block">Univers suivant</span>
              <span className="display mt-3 block text-[clamp(2rem,5vw,4rem)] transition group-hover:text-[var(--accent-ink)]">{next.name}</span>
            </span>
            <ArrowRight className="size-10 shrink-0 text-[var(--accent-ink)] transition-transform duration-500 group-hover:translate-x-2" aria-hidden />
          </LocaleLink>
        </section>
      )}

      <CtaBlock data={universe.isUpcoming
        ? { title: `${universe.name} : bientôt à l'EMSI`, text: "Laissez-nous vos coordonnées : nous vous préviendrons dès l'ouverture des inscriptions.", buttons: [{ label: "Nous écrire", url: "/contact", style: "primary" }] }
        : { title: `Rejoignez l'univers ${universe.name}`, text: "Candidatez en ligne en quelques minutes : l'équipe de l'EMSI vous répond.", buttons: [{ label: "Candidater", url: "/candidater", style: "primary" }, { label: "Nous contacter", url: "/contact", style: "secondary" }] }} />
    </div>
  );
}
