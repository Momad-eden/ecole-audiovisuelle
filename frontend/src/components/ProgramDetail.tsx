import { Check } from "lucide-react";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { MediaImage } from "@/components/ui/MediaImage";
import { RichText } from "@/components/ui/RichText";
import { Section } from "@/components/ui/Section";
import type { Program } from "@/lib/types";
import { getDictionary } from "@/lib/i18n";
import { formatDate, formatMoney } from "@/lib/i18n/format";
import type { Locale } from "@/lib/i18n/locales";

function List({ title, items }: { title: string; items?: string[] }) {
  if (!items || items.length === 0) return null;
  return (
    <div>
      <h2 className="cartel mb-4">{title}</h2>
      <ul className="space-y-3">
        {items.map((item) => (
          <li key={item} className="flex gap-3"><Check className="mt-1 size-4 shrink-0 text-[var(--accent-ink)]" aria-hidden />{item}</li>
        ))}
      </ul>
    </div>
  );
}

export function ProgramDetail({ program, applyHref, locale }: { program: Program; applyHref: string; locale: Locale }) {
  const t = getDictionary(locale).program;
  const date = (iso: string) => formatDate(iso, locale);
  const cohorts = program.cohorts ?? [];

  return (
    <>
      <section className="beam relative isolate overflow-hidden pb-16 pt-40">
        <div className="absolute inset-0 -z-10">
          <MediaImage image={program.cover} sizes="100vw" priority className="opacity-25" />
          <div className="absolute inset-0 bg-gradient-to-b from-night/30 to-night" />
        </div>
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <p className="cartel">{[program.kindLabel, program.durationLabel].filter(Boolean).join(" · ")}</p>
          <h1 className="display mt-5 max-w-5xl text-[clamp(2.2rem,5vw,4.4rem)] text-balance">{program.title}</h1>
          {program.levelLabel && <p className="mt-4 text-lg text-[var(--accent-ink)]">{program.levelLabel}</p>}
          {program.summary && <p className="mt-6 max-w-3xl text-lg text-ink/80">{program.summary}</p>}
          <div className="mt-10 flex flex-wrap gap-3">
            {program.acceptsApplications ? (
              <ButtonLink href={applyHref}>{t.apply}</ButtonLink>
            ) : (
              <p className="rounded-full border border-line px-5 py-3 text-sm text-ink-muted">{t.closed}</p>
            )}
          </div>
        </div>
      </section>

      <Section>
        <div className="grid gap-16 lg:grid-cols-3">
          <div className="lg:col-span-2">
            <RichText html={program.description} locale={locale} className="text-lg" />
          </div>
          <aside className="space-y-10">
            <List title={t.skills} items={program.skills} />
            <List title={t.outcomes} items={program.outcomes} />
            <List title={t.prerequisites} items={program.prerequisites} />
            <List title={t.equipment} items={program.equipment} />
          </aside>
        </div>
      </Section>

      {cohorts.length > 0 && (
        <Section>
          <h2 className="display mb-8 text-[clamp(1.8rem,3.4vw,2.6rem)]">{t.sessions}</h2>
          <div className="space-y-6">
            {cohorts.map((cohort) => (
              <article key={cohort.id} className="rounded-3xl border border-line bg-night-2 p-6 sm:p-8">
                <div className="flex flex-wrap items-baseline justify-between gap-4">
                  <h3 className="display text-xl">{cohort.name}</h3>
                  <span className="rounded-full border border-line px-3 py-1 text-sm">{cohort.statusLabel}</span>
                </div>
                <p className="mt-2 text-ink-muted">
                  {cohort.startsOn && t.from(date(cohort.startsOn))}{cohort.endsOn && t.to(date(cohort.endsOn))}
                  {cohort.applicationsOpenAt && !cohort.acceptsApplications && t.opensOn(date(cohort.applicationsOpenAt))}
                  {cohort.applicationsCloseAt && cohort.acceptsApplications && t.closesOn(date(cohort.applicationsCloseAt))}
                </p>
                {cohort.offerings && cohort.offerings.length > 0 && (
                  <div className="mt-6 overflow-x-auto">
                    <table className="w-full min-w-[32rem] text-left text-sm">
                      <thead className="cartel"><tr><th className="py-2 font-normal">{t.track}</th><th className="py-2 font-normal">{t.seats}</th><th className="py-2 font-normal">{t.fees}</th></tr></thead>
                      <tbody className="divide-y divide-line">
                        {cohort.offerings.map((offering) => (
                          <tr key={offering.id}>
                            <td className="py-3">{offering.track?.name ?? "—"}</td>
                            <td className="py-3">{offering.capacity ?? "—"}</td>
                            <td className="py-3">{offering.feeAmount > 0 ? formatMoney(offering.feeAmount, locale) : offering.fundingLabel}</td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>
                )}
              </article>
            ))}
          </div>
        </Section>
      )}
    </>
  );
}
