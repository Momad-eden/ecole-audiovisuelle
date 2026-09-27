import { Check } from "lucide-react";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { MediaImage } from "@/components/ui/MediaImage";
import { RichText } from "@/components/ui/RichText";
import { Section } from "@/components/ui/Section";
import type { Program } from "@/lib/types";
import { fcfa, formatDate } from "@/lib/utils";

function List({ title, items }: { title: string; items?: string[] }) {
  if (!items || items.length === 0) return null;
  return (
    <div>
      <h2 className="cartel mb-4">{title}</h2>
      <ul className="space-y-3">
        {items.map((item) => (
          <li key={item} className="flex gap-3"><Check className="mt-1 size-4 shrink-0 text-[var(--accent)]" aria-hidden />{item}</li>
        ))}
      </ul>
    </div>
  );
}

export function ProgramDetail({ program, applyHref }: { program: Program; applyHref: string }) {
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
          <h1 className="mt-4 max-w-4xl font-display text-4xl leading-tight font-medium text-balance sm:text-6xl">{program.title}</h1>
          {program.levelLabel && <p className="mt-4 text-lg text-[var(--accent)]">{program.levelLabel}</p>}
          {program.summary && <p className="mt-6 max-w-3xl text-lg text-ink/80">{program.summary}</p>}
          <div className="mt-10 flex flex-wrap gap-3">
            {program.acceptsApplications ? (
              <ButtonLink href={applyHref}>Candidater à cette formation</ButtonLink>
            ) : (
              <p className="rounded-full border border-line px-5 py-3 text-sm text-ink-muted">Candidatures fermées pour le moment — voir le calendrier ci-dessous.</p>
            )}
          </div>
        </div>
      </section>

      <Section>
        <div className="grid gap-16 lg:grid-cols-3">
          <div className="lg:col-span-2">
            <RichText html={program.description} className="text-lg" />
          </div>
          <aside className="space-y-10">
            <List title="Compétences visées" items={program.skills} />
            <List title="Débouchés" items={program.outcomes} />
            <List title="Prérequis" items={program.prerequisites} />
            <List title="Équipements" items={program.equipment} />
          </aside>
        </div>
      </Section>

      {cohorts.length > 0 && (
        <Section>
          <h2 className="mb-8 font-display text-3xl">Sessions</h2>
          <div className="space-y-6">
            {cohorts.map((cohort) => (
              <article key={cohort.id} className="rounded-3xl border border-line bg-night-2 p-6 sm:p-8">
                <div className="flex flex-wrap items-baseline justify-between gap-4">
                  <h3 className="font-display text-2xl">{cohort.name}</h3>
                  <span className="rounded-full border border-line px-3 py-1 text-sm">{cohort.statusLabel}</span>
                </div>
                <p className="mt-2 text-ink-muted">
                  {cohort.startsOn && `Du ${formatDate(cohort.startsOn)}`}{cohort.endsOn && ` au ${formatDate(cohort.endsOn)}`}
                  {cohort.applicationsOpenAt && !cohort.acceptsApplications && ` · Candidatures à partir du ${formatDate(cohort.applicationsOpenAt)}`}
                  {cohort.applicationsCloseAt && cohort.acceptsApplications && ` · Candidatures jusqu'au ${formatDate(cohort.applicationsCloseAt)}`}
                </p>
                {cohort.offerings && cohort.offerings.length > 0 && (
                  <div className="mt-6 overflow-x-auto">
                    <table className="w-full min-w-[32rem] text-left text-sm">
                      <thead className="cartel"><tr><th className="py-2 font-normal">Filière</th><th className="py-2 font-normal">Places</th><th className="py-2 font-normal">Frais</th></tr></thead>
                      <tbody className="divide-y divide-line">
                        {cohort.offerings.map((offering) => (
                          <tr key={offering.id}>
                            <td className="py-3">{offering.track?.name ?? "—"}</td>
                            <td className="py-3">{offering.capacity ?? "—"}</td>
                            <td className="py-3">{offering.feeAmount > 0 ? fcfa(offering.feeAmount) : offering.fundingLabel}</td>
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
