import { Fragment } from "react";
import { Emphasis } from "@/components/ui/Emphasis";
import { cn } from "@/lib/utils";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { MediaImage } from "@/components/ui/MediaImage";
import { Container, Section, SectionTitle } from "@/components/ui/Section";
import { InView } from "@/components/motion/InView";
import { Reveal } from "@/components/motion/Reveal";
import { getDictionary } from "@/lib/i18n";
import type { BlockProps, EquipmentData, MarqueeData, VenueData } from "./types";

/** Bandeau de grands mots qui défile (immobile en mouvement réduit). */
export function MarqueeBlock({ data }: BlockProps<MarqueeData>) {
  const words = (data.words ?? []).filter(Boolean);
  if (words.length === 0) return null;
  const sequence = [...words, ...words, ...words];

  return (
    <section className="relative border-y border-line bg-night-2 py-6 sm:py-8">
      <p className="sr-only">{words.join(", ")}</p>
      <InView className="overflow-hidden" >
        <div className="flex w-max animate-marquee" aria-hidden>
          {[0, 1].map((copy) => (
            <div key={copy} className="flex shrink-0 items-center">
              {sequence.map((word, i) => (
                <Fragment key={`${copy}-${i}`}>
                  <span className={`display px-6 text-[clamp(2.5rem,7vw,6rem)] ${i % 2 ? "text-outline" : ""}`}>{word}</span>
                  <span className="size-3 rounded-full bg-brand shadow-[0_0_20px_var(--color-brand)]" />
                </Fragment>
              ))}
            </div>
          ))}
        </div>
      </InView>
    </section>
  );
}

/** Le lieu : l'école au cœur du Grand Théâtre National. */
export function VenueBlock({ data }: BlockProps<VenueData>) {
  const facts = data.facts ?? [];
  return (
    <section className="relative isolate overflow-hidden py-24 sm:py-32" style={{ ["--accent" as string]: "var(--color-gold)" }}>
      <p className="display text-outline pointer-events-none absolute inset-x-0 -top-2 -z-10 overflow-hidden whitespace-nowrap text-center text-[clamp(3rem,8.4vw,9rem)] opacity-50" aria-hidden>
        Grand Théâtre
      </p>
      <Container>
        <div className="grid items-center gap-14 lg:grid-cols-[1fr_1.1fr]">
          <Reveal>
            {data.eyebrow && <p className="cartel mb-5 flex items-center gap-3 text-[var(--accent-ink)]"><span className="h-px w-10 bg-[var(--accent-ink)]" aria-hidden />{data.eyebrow}</p>}
            <h2 className="display text-[clamp(1.9rem,3.4vw,3rem)] text-balance"><Emphasis text={data.title} /></h2>
            {data.text && <p className="mt-6 max-w-xl text-lg text-ink/80">{data.text}</p>}
            {data.buttons && data.buttons.length > 0 && (
              <div className="mt-8 flex flex-wrap gap-3">
                {data.buttons.map((b) => <ButtonLink key={b.url} href={b.url} variant={b.style === "primary" ? "primary" : "secondary"}>{b.label}</ButtonLink>)}
              </div>
            )}
          </Reveal>

          <Reveal delay={150} className="relative">
            <div className="relative aspect-[4/3] overflow-hidden rounded-[2rem] border border-line bg-night-2">
              {data.image ? (
                <MediaImage image={data.image} sizes="(min-width: 1024px) 55vw, 100vw" />
              ) : (
                <InView className="absolute inset-0"><TheatreArt /></InView>
              )}
              <div className="pointer-events-none absolute inset-0 rounded-[2rem] ring-1 ring-inset ring-ink/10" aria-hidden />
            </div>
          </Reveal>
        </div>

        {facts.length > 0 && (
          <dl className={cn("mt-16 grid gap-px overflow-hidden rounded-3xl border border-line bg-line", facts.length === 2 && "sm:grid-cols-2", facts.length === 3 && "sm:grid-cols-3", facts.length >= 4 && "sm:grid-cols-2 lg:grid-cols-4")}>
            {facts.map((fact, i) => (
              <Reveal key={fact.label} delay={i * 100} className="bg-night p-8">
                <dt className="sr-only">{fact.label}</dt>
                <dd className="display text-[clamp(2.4rem,5vw,3.8rem)] text-[var(--accent-ink)]">{fact.value}</dd>
                <dd className="mt-2 text-ink-muted">{fact.label}</dd>
              </Reveal>
            ))}
          </dl>
        )}
      </Container>
    </section>
  );
}

/** Dessin d'une scène à l'italienne (cadre, rideaux, poursuite) en attendant une photo du lieu. */
function TheatreArt() {
  const folds = Array.from({ length: 7 }, (_, i) => i);
  return (
    <svg viewBox="0 0 400 300" className="h-full w-full" aria-hidden focusable="false">
      <defs>
        <radialGradient id="venue-spot" cx="50%" cy="100%" r="60%">
          <stop offset="0" stopColor="var(--color-gold)" stopOpacity="0.55" />
          <stop offset="1" stopColor="var(--color-gold)" stopOpacity="0" />
        </radialGradient>
        <linearGradient id="venue-cone" x1="0" x2="0" y1="0" y2="1">
          <stop offset="0" stopColor="var(--color-ink)" stopOpacity="0.35" />
          <stop offset="1" stopColor="var(--color-ink)" stopOpacity="0" />
        </linearGradient>
      </defs>
      <rect width="400" height="300" fill="var(--color-night-2)" />
      <path d="M40 290 V70 Q200 10 360 70 V290" fill="none" stroke="var(--color-gold)" strokeOpacity="0.6" strokeWidth="2" />
      <path d="M58 290 V82 Q200 30 342 82 V290" fill="none" stroke="var(--color-gold)" strokeOpacity="0.25" />
      {folds.map((i) => (
        <g key={i}>
          <path d={`M${60 + i * 9} 84 Q${64 + i * 9} 190 ${58 + i * 9} 290`} stroke="var(--color-rec)" strokeOpacity={0.55 - i * 0.06} fill="none" strokeWidth="6" />
          <path d={`M${340 - i * 9} 84 Q${336 - i * 9} 190 ${342 - i * 9} 290`} stroke="var(--color-rec)" strokeOpacity={0.55 - i * 0.06} fill="none" strokeWidth="6" />
        </g>
      ))}
      <path className="uv-beam" d="M196 40 L150 262 L250 262 L204 40 Z" fill="url(#venue-cone)" style={{ animationDuration: "7s" }} />
      <ellipse cx="200" cy="262" rx="120" ry="20" fill="url(#venue-spot)" />
      <line x1="110" x2="290" y1="262" y2="262" stroke="var(--color-ink)" strokeOpacity="0.3" />
      <text x="200" y="286" textAnchor="middle" fill="var(--color-gold)" style={{ fontFamily: "var(--font-mono)", fontSize: 9, letterSpacing: "0.2em" }}>GRAND THÉÂTRE · DAKAR</text>
    </svg>
  );
}

/** Le matériel, présenté comme une fiche technique. */
export function EquipmentBlock({ data, locale }: BlockProps<EquipmentData>) {
  const groups = (data.groups ?? []).filter((g) => g.items && g.items.length > 0);
  if (groups.length === 0) return null;

  return (
    <Section className="relative">
      <SectionTitle eyebrow={getDictionary(locale).impact.techSheet} title={data.title} text={data.text} />
      <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        {groups.map((group, index) => (
          <Reveal key={group.category} delay={(index % 3) * 100} className="group relative overflow-hidden rounded-3xl border border-line bg-night-2 transition duration-500 hover:border-brand/60">
            {group.image && (
              <div className="relative aspect-[16/9] border-b border-line">
                <MediaImage image={group.image} sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw" />
              </div>
            )}
            <div className="p-7">
              <p className="cartel flex items-center justify-between">
                <span>{group.category}</span>
                <span className="tabular-nums text-brand">{`A${index + 1}`}</span>
              </p>
              <ul className="mt-5 space-y-3">
                {group.items!.map((item) => (
                  <li key={item} className="flex items-baseline gap-3">
                    <span className="size-1.5 shrink-0 translate-y-[-2px] rounded-full bg-brand shadow-[0_0_10px_var(--color-brand)]" aria-hidden />
                    <span className="font-medium">{item}</span>
                    <span className="flex-1 border-b border-dotted border-line" aria-hidden />
                  </li>
                ))}
              </ul>
            </div>
          </Reveal>
        ))}
      </div>
    </Section>
  );
}
