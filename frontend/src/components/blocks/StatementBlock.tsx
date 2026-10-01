import { CountUp } from "@/components/motion/CountUp";
import { Reveal } from "@/components/motion/Reveal";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { Emphasis } from "@/components/ui/Emphasis";
import { MediaImage } from "@/components/ui/MediaImage";
import { cn } from "@/lib/utils";
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
      <div className="absolute inset-x-0 top-0 -z-10 h-[70%] bg-[radial-gradient(60%_80%_at_20%_0%,color-mix(in_oklab,var(--accent)_16%,transparent),transparent_70%)]" aria-hidden />
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <Reveal className="grid gap-12 lg:grid-cols-[1.9fr_1fr] lg:items-end">
          <div>
            {data.eyebrow && <p className="cartel mb-6 flex items-center gap-3 text-[var(--accent-ink)]"><span className="h-px w-10 bg-[var(--accent-ink)]" aria-hidden />{data.eyebrow}</p>}
            <p className="display text-balance text-[clamp(1.9rem,3.5vw,3.2rem)] leading-[1.04]"><Emphasis text={data.text} /></p>
            {data.buttonLabel && data.buttonUrl && <div className="mt-10"><ButtonLink href={data.buttonUrl} variant="secondary">{data.buttonLabel}</ButtonLink></div>}
          </div>
          {facts.length > 0 && (
            <dl className={cn("grid gap-x-8 gap-y-8 border-t border-line pt-8 lg:border-l lg:border-t-0 lg:pl-10 lg:pt-0", facts.length > 1 && "grid-cols-2")}>
              {facts.map((fact) => (
                <div key={fact.value + fact.label} className="flex flex-col">
                  <dt className="cartel order-2 mt-3 text-ink-muted">{fact.label}</dt>
                  <dd className="display order-1 text-[clamp(2.6rem,4.6vw,4rem)] leading-none text-[var(--accent-ink)]"><CountUp value={fact.value} /></dd>
                </div>
              ))}
            </dl>
          )}
        </Reveal>
      </div>

      {images.length > 0 && (
        <div className="statement-ribbon mt-16 sm:mt-20" aria-hidden>
          <div className="statement-track">
            {[...images, ...images].map((image, index) => (
              <div key={image.url + index} className={cn("relative h-52 w-72 shrink-0 overflow-hidden rounded-2xl border border-line sm:h-64 sm:w-96", index % 2 === 1 && "sm:translate-y-6")}>
                <MediaImage image={{ ...image, alt: "" }} sizes="384px" />
              </div>
            ))}
          </div>
        </div>
      )}
    </section>
  );
}
