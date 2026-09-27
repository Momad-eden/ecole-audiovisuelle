"use client";

import { useEffect, useRef, useState } from "react";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { MediaImage } from "@/components/ui/MediaImage";
import { useReducedMotion } from "@/components/motion/useReducedMotion";
import { HeroVideo } from "./HeroVideo";
import { ConsoleArt, LineArrayArt } from "./HeroArt";
import { InView } from "@/components/motion/InView";
import type { HeroData } from "./types";

type Variant = "stage" | "studio" | "events";

/** Couleurs reprises tour à tour par les mots qui défilent, selon le décor. */
const WORD_COLORS: Record<Variant, string[]> = {
  stage: ["var(--color-violet)", "var(--color-hmi)", "var(--color-gold)", "var(--color-magenta)", "var(--color-brand)"],
  studio: ["var(--color-rec)", "var(--color-gold)", "var(--color-brand)", "var(--color-violet)"],
  events: ["var(--color-gold)", "var(--color-brand)", "var(--color-hmi)"],
};

const BEAMS: Record<Variant, { left: string; beam: string; from: string; to: string; sweep: string }[]> = {
  stage: [
    { left: "4%", beam: "var(--color-brand)", from: "-22deg", to: "10deg", sweep: "13s" },
    { left: "38%", beam: "var(--color-violet)", from: "14deg", to: "-16deg", sweep: "10s" },
    { left: "70%", beam: "var(--color-hmi)", from: "-8deg", to: "24deg", sweep: "15s" },
  ],
  studio: [{ left: "30%", beam: "var(--color-rec)", from: "-10deg", to: "10deg", sweep: "16s" }],
  events: [
    { left: "18%", beam: "var(--color-gold)", from: "-20deg", to: "14deg", sweep: "9s" },
    { left: "46%", beam: "var(--color-brand)", from: "16deg", to: "-18deg", sweep: "11s" },
    { left: "62%", beam: "var(--color-gold)", from: "-12deg", to: "22deg", sweep: "8s" },
  ],
};

const ACCENT: Record<Variant, string | undefined> = { stage: undefined, studio: "var(--color-rec)", events: "var(--color-gold)" };

/**
 * Héros « scène » : la page s'ouvre sur un plateau dans le noir balayé par des projecteurs,
 * une poursuite suit le pointeur et la fin du titre change (le son, l'image…).
 * En mouvement réduit, tout est figé et le premier mot reste affiché.
 */
export function StageHero({ data, first, variant = "stage" }: { data: HeroData; first: boolean; variant?: Variant }) {
  const reducedMotion = useReducedMotion();
  const words = (data.words ?? []).filter(Boolean);
  const [index, setIndex] = useState(0);
  const sectionRef = useRef<HTMLElement>(null);
  const timecodeRef = useRef<HTMLSpanElement>(null);
  const Heading = first ? "h1" : "h2";

  useEffect(() => {
    if (reducedMotion || words.length < 2) return;
    const id = window.setInterval(() => setIndex((i) => (i + 1) % words.length), 2400);
    return () => window.clearInterval(id);
  }, [reducedMotion, words.length]);

  // Timecode REC à 25 images/s, écrit directement dans le DOM (aucun rendu React), arrêté hors écran.
  useEffect(() => {
    const section = sectionRef.current;
    if (reducedMotion || !section) return;
    const start = performance.now();
    const pad = (n: number) => String(n).padStart(2, "0");
    let timer = 0;
    const tick = () => {
      const total = Math.floor(((performance.now() - start) / 1000) * 25);
      const f = total % 25, s = Math.floor(total / 25) % 60, m = Math.floor(total / 1500) % 60;
      if (timecodeRef.current) timecodeRef.current.textContent = `00:${pad(m)}:${pad(s)}:${pad(f)}`;
    };
    const observer = new IntersectionObserver(([entry]) => {
      window.clearInterval(timer);
      if (entry.isIntersecting) timer = window.setInterval(tick, 40);
    });
    observer.observe(section);
    return () => {
      observer.disconnect();
      window.clearInterval(timer);
    };
  }, [reducedMotion]);

  function followSpot(event: React.PointerEvent<HTMLElement>) {
    if (reducedMotion || event.pointerType !== "mouse" || !sectionRef.current) return;
    const rect = sectionRef.current.getBoundingClientRect();
    sectionRef.current.style.setProperty("--spot-x", `${event.clientX - rect.left}px`);
    sectionRef.current.style.setProperty("--spot-y", `${event.clientY - rect.top}px`);
  }

  const word = words[index] ?? "";

  return (
    <section
      ref={sectionRef}
      onPointerMove={followSpot}
      className="relative isolate flex min-h-[100svh] flex-col justify-end overflow-hidden pb-10 pt-32"
      style={data.accent || ACCENT[variant] ? { ["--accent" as string]: data.accent || ACCENT[variant] } : undefined}
    >
      <div className="absolute inset-0 -z-10" aria-hidden>
        {data.image && <MediaImage image={data.image} sizes="100vw" priority={first} className="opacity-30 mix-blend-luminosity" />}
        {data.videoLoop && <HeroVideo src={data.videoLoop} poster={data.image?.url} className="opacity-30 mix-blend-luminosity" />}
        {variant === "events" && <InView className="absolute inset-0 opacity-80"><LineArrayArt /></InView>}
        {BEAMS[variant].map((b) => (
          <div key={b.left} className="stage-beam" style={{ left: b.left, ["--beam" as string]: b.beam, ["--from" as string]: b.from, ["--to" as string]: b.to, ["--sweep" as string]: b.sweep }} />
        ))}
        <div className="follow-spot" />
        <div className="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-night via-night/80 to-transparent" />
        {variant === "studio" && <InView className="absolute inset-0 opacity-35 sm:opacity-60"><ConsoleArt /></InView>}
        {variant === "studio" && <div className="absolute inset-x-0 top-0 h-1/2 bg-gradient-to-b from-night to-transparent" />}
        {variant === "studio" && <div className="absolute inset-y-0 left-0 w-full bg-gradient-to-r from-night via-night/80 to-transparent sm:w-3/4" />}
        <div className="absolute inset-x-0 bottom-[22%] h-px bg-gradient-to-r from-transparent via-ink/20 to-transparent" />
      </div>

      <div className="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
        {data.eyebrow && (
          <p className="cartel mb-6 flex items-center gap-3 text-ink/80">
            <span className="h-px w-10 bg-brand" aria-hidden />
            {data.eyebrow}
          </p>
        )}

        <Heading className="display text-[clamp(2.6rem,9vw,8.5rem)] text-balance">
          {words.length > 0 ? (
            <>
              <span className="sr-only">{`${data.title} ${words.join(", ")}`}</span>
              <span aria-hidden>
                <span className="block">{data.title}</span>
                <span key={word} className="word-in block" style={{ color: WORD_COLORS[variant][index % WORD_COLORS[variant].length], textShadow: "0 0 60px currentColor" }}>
                  {word}
                  <span className="text-ink">.</span>
                </span>
              </span>
            </>
          ) : (
            data.title
          )}
        </Heading>

        <div className="mt-10 grid gap-10 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
          {data.subtitle && <p className="max-w-2xl text-lg text-ink/80 sm:text-xl">{data.subtitle}</p>}
          {data.buttons && data.buttons.length > 0 && (
            <div className="flex flex-wrap gap-3">
              {data.buttons.map((button) => (
                <ButtonLink key={button.url + button.label} href={button.url} variant={button.style === "secondary" ? "secondary" : "primary"} size="lg">{button.label}</ButtonLink>
              ))}
            </div>
          )}
        </div>

        <div className="cartel mt-16 flex flex-wrap items-center justify-between gap-4 border-t border-line pt-5" aria-hidden>
          <span className="flex items-center gap-2 text-ink/80">
            <span className="rec-dot size-2 rounded-full bg-rec" />
            REC <span ref={timecodeRef} className="tabular-nums">00:00:00:00</span>
          </span>
          <span className="hidden sm:inline">{variant === "studio" ? "Enregistrement · Mixage · Mastering" : variant === "events" ? "Sonorisation · Lumière · Podiums" : "Son · Image · Lumière · Design · Scène"}</span>
          <span>Défiler ↓</span>
        </div>
      </div>
    </section>
  );
}
