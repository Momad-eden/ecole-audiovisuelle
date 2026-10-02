import { CountUp } from "@/components/motion/CountUp";
import { Reveal } from "@/components/motion/Reveal";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { Emphasis } from "@/components/ui/Emphasis";
import { cn } from "@/lib/utils";
import { StatementRibbon } from "./StatementRibbon";
import type { StatementData } from "./types";

/**
 * « Manifeste » : la grande phrase qui présente la plateforme (mots mis en valeur), ses chiffres clés qui
 * défilent jusqu'à leur valeur, et un bandeau de photos qui glisse en continu sous le texte (pause au survol,
 * immobile et défilable à la main si le visiteur limite les animations). Pensé pour suivre le film d'ouverture.
 */
export function StatementBlock({ data }: { data: StatementData }) {
  const facts = data.facts ?? [];
  const images = data.images ?? [];

  return (
    <section data-testid="statement" className="relative isolate overflow-hidden py-24 sm:py-32">
      <div className="absolute inset-x-0 top-0 -z-10 h-[70%] bg-[radial-gradient(60%_80%_at_20%_0%,color-mix(in_oklab,var(--accent)_14%,transparent),transparent_70%)]" aria-hidden />
      <div className="mx-auto max-w-7xl px-5 sm:px-6 lg:px-8">
        <Reveal className="grid gap-8 lg:grid-cols-[1fr_auto] lg:items-end">
          <div className="max-w-5xl">
            {data.eyebrow && <p className="cartel mb-6 flex items-center gap-3 text-[var(--accent-ink)]"><span className="h-px w-10 bg-[var(--accent-ink)]" aria-hidden />{data.eyebrow}</p>}
            <p className="display text-balance text-[clamp(2rem,4vw,3.6rem)] leading-[1.08]"><Emphasis text={data.text} /></p>
          </div>
          {data.buttonLabel && data.buttonUrl && <div className="lg:pb-2"><ButtonLink href={data.buttonUrl} variant="secondary">{data.buttonLabel}</ButtonLink></div>}
        </Reveal>

        {facts.length > 0 && (
          <dl className={cn("mt-14 grid gap-3 sm:mt-16 sm:gap-4", facts.length > 1 && "grid-cols-2", facts.length === 3 && "lg:grid-cols-3", facts.length >= 4 && "lg:grid-cols-4")}>
            {facts.map((fact, index) => (
              <Reveal key={fact.value + fact.label} delay={index * 80} className="flex flex-col justify-between gap-6 rounded-3xl border border-line bg-night-2 p-6 sm:p-8">
                <dt className="cartel order-2 text-ink-muted">{fact.label}</dt>
                <dd className="display order-1 text-[clamp(2.6rem,4.4vw,4.2rem)] leading-none text-[var(--accent-ink)]"><CountUp value={fact.value} /></dd>
              </Reveal>
            ))}
          </dl>
        )}
      </div>

      {images.length > 0 && <StatementRibbon images={images} />}
    </section>
  );
}
