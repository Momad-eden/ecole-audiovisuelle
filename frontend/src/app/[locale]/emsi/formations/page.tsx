import type { Metadata } from "next";
import { LocaleLink } from "@/components/i18n/LocaleLink";
import { ArrowRight } from "lucide-react";
import { CtaBlock } from "@/components/blocks/ContentBlocks";
import { Reveal } from "@/components/motion/Reveal";
import { ProgramCard } from "@/components/ProgramCard";
import { PageHeader } from "@/components/ui/PageHeader";
import { Section, SectionTitle } from "@/components/ui/Section";
import { api } from "@/lib/api";
import { getDictionary } from "@/lib/i18n";
import type { Locale } from "@/lib/i18n/locales";

type Props = { params: Promise<{ locale: Locale }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  return getDictionary((await params).locale).meta.programs;
}

export default async function ProgramsPage({ params }: Props) {
  const { locale } = await params;
  const { programsPage: t, blocks, common } = getDictionary(locale);
  const [universes, programs] = await Promise.all([api.rooms(locale), api.programs(undefined, locale)]);
  const withTracks = universes.filter((u) => !u.isUpcoming && (u.tracks ?? []).length > 0);
  const school = programs.filter((p) => p.audience === "school");
  const professional = programs.filter((p) => p.audience === "professional");

  return (
    <>
      <PageHeader eyebrow={t.eyebrow} title={t.title} text={t.text} />

      <Section className="pt-0 sm:pt-0">
        <div className="space-y-16">
          {withTracks.map((universe) => (
            <section key={universe.id} aria-labelledby={`univers-${universe.slug}`} style={{ ["--accent" as string]: universe.accentColor }}>
              <div className="mb-6 flex flex-wrap items-end justify-between gap-4 border-b border-line pb-5">
                <h2 id={`univers-${universe.slug}`} className="display flex items-center gap-4 text-[clamp(1.8rem,3.5vw,2.8rem)]">
                  <span className="size-3 rounded-full bg-[var(--accent-ink)] shadow-[0_0_18px_var(--accent)]" aria-hidden />
                  {universe.name}
                </h2>
                <LocaleLink href={`/emsi/univers/${universe.slug}`} className="group inline-flex items-center gap-2 text-sm font-semibold text-[var(--accent-ink)]">
                  {t.discoverUniverse} <ArrowRight className="size-4 transition-transform group-hover:translate-x-1" aria-hidden />
                </LocaleLink>
              </div>
              <ul className="grid gap-5 md:grid-cols-2">
                {(universe.tracks ?? []).map((track, i) => (
                  <Reveal as="li" key={track.id} delay={i * 100} className={(universe.tracks ?? []).length === 1 ? "md:col-span-2" : undefined}>
                    <LocaleLink href={`/emsi/univers/${universe.slug}#filieres`} className="group flex h-full flex-col rounded-3xl border border-line bg-night-2 p-7 transition duration-500 hover:-translate-y-1 hover:border-[var(--accent)]">
                      <p className="cartel text-[var(--accent-ink)]">{t.track}</p>
                      <h3 className="display mt-3 text-2xl">{track.name}</h3>
                      {track.summary && <p className="mt-3 line-clamp-3 text-ink-muted">{track.summary}</p>}
                      {track.outcomes.length > 0 && (
                        <p className="mt-5 text-sm text-ink/80"><span className="cartel mr-2">{t.jobs}</span>{track.outcomes.slice(0, 3).join(" · ")}</p>
                      )}
                      <span className="mt-auto inline-flex items-center gap-2 pt-6 text-sm font-semibold text-[var(--accent-ink)]">{t.seeDetail} <ArrowRight className="size-4 transition-transform group-hover:translate-x-1" aria-hidden /></span>
                    </LocaleLink>
                  </Reveal>
                ))}
              </ul>
            </section>
          ))}
        </div>
      </Section>

      {school.length > 0 && (
        <Section>
          <SectionTitle eyebrow={t.eyebrow} title={t.ourPrograms} />
          <div className="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            {school.map((program) => <ProgramCard key={program.id} program={program} locale={locale} />)}
          </div>
        </Section>
      )}

      {professional.length > 0 && (
        <Section>
          <SectionTitle eyebrow={blocks.professionalSpace} title={t.professionalTitle} text={t.professionalText} />
          <div className="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            {professional.map((program) => <ProgramCard key={program.id} program={program} locale={locale} />)}
          </div>
        </Section>
      )}

      <CtaBlock data={{ title: t.ctaTitle, text: t.ctaText, buttons: [{ label: common.apply, url: "/candidater", style: "primary" }, { label: common.contactUs, url: "/contact", style: "secondary" }] }} />
    </>
  );
}
