"use client";

import { plainTitle } from "@/lib/emphasis";
import { Emphasis } from "@/components/ui/Emphasis";
import { useEffect, useRef, useState } from "react";
import { LocaleLink } from "@/components/i18n/LocaleLink";
import { useT } from "@/components/i18n/LocaleProvider";
import { ArrowRight, Pause, Play } from "lucide-react";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { MediaImage } from "@/components/ui/MediaImage";
import { useReducedMotion } from "@/components/motion/useReducedMotion";
import { accentVars } from "@/lib/contrast";
import { cn, frenchSpacing } from "@/lib/utils";
import type { HeroData } from "./types";

/** Durée d'une diapositive, en millisecondes. */
const DURATION = 6000;

/**
 * « Cinéma » : des photos plein écran qui se succèdent en fondu avec un lent zoom, des barres de
 * progression cliquables, et un bandeau de chiffres clés. Pause au survol, au focus et par un
 * bouton ; flèches du clavier ; rien ne défile seul si l'internaute limite les animations.
 */
export function CinemaHero({ data, first }: { data: HeroData; first: boolean }) {
  const reducedMotion = useReducedMotion();
  const { cinema: t, common } = useT();
  const slides = data.slides ?? [];
  const count = slides.length;
  const [index, setIndex] = useState(0);
  const [paused, setPaused] = useState(false);
  const [hovered, setHovered] = useState(false);
  const [focused, setFocused] = useState(false);
  const [visible, setVisible] = useState(true);
  const sectionRef = useRef<HTMLElement>(null);
  const Heading = first ? "h1" : "h2";
  const running = count > 1 && !reducedMotion && !paused && !hovered && !focused && visible;

  useEffect(() => {
    const section = sectionRef.current;
    if (!section) return;
    const observer = new IntersectionObserver(([entry]) => setVisible(entry.isIntersecting));
    observer.observe(section);
    return () => observer.disconnect();
  }, []);

  useEffect(() => {
    if (!running) return;
    const timer = window.setTimeout(() => setIndex((i) => (i + 1) % count), DURATION);
    return () => window.clearTimeout(timer);
  }, [running, index, count]);

  const go = (next: number) => setIndex(((next % count) + count) % count);

  // Pause au survol de la souris, limitée au titre et aux commandes (pas à toute la photo, ni au toucher).
  const hoverPause = {
    onPointerEnter: (event: React.PointerEvent) => event.pointerType === "mouse" && setHovered(true),
    onPointerLeave: () => setHovered(false),
  };

  function onKeyDown(event: React.KeyboardEvent<HTMLElement>) {
    if (count < 2) return;
    if (event.key === "ArrowRight") { event.preventDefault(); go(index + 1); }
    if (event.key === "ArrowLeft") { event.preventDefault(); go(index - 1); }
  }

  const facts = data.facts ?? [];
  const buttons = data.buttons ?? [];

  return (
    <section
      ref={sectionRef}
      data-testid="cinema-hero"
      data-first={first ? "" : undefined}
      tabIndex={count > 1 ? 0 : undefined}
      aria-roledescription={count > 1 ? t.slideshow : undefined}
      aria-label={plainTitle(data.title)}
      onKeyDown={onKeyDown}
      // Pause au focus seulement au clavier : un clic sur la photo ne doit pas figer le diaporama.
      onFocus={(event) => event.target.matches(":focus-visible") && setFocused(true)}
      onBlur={(event) => !event.currentTarget.contains(event.relatedTarget) && setFocused(false)}
      className="scene-dark relative isolate flex min-h-[92svh] flex-col overflow-hidden bg-night outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-[var(--accent-ink)]"
      style={accentVars(data.accent)}
    >
      {slides.map((slide, i) => (
        <div key={slide.image.url + i} className={cn("absolute inset-0 -z-20 transition-opacity duration-[1400ms]", i === index ? "opacity-100" : "opacity-0")} aria-hidden={i !== index}>
          <div className={cn("absolute inset-0", i === index && !reducedMotion && "cinema-zoom")} style={{ animationPlayState: running ? "running" : "paused" }}>
            <MediaImage image={slide.image} sizes="100vw" priority={first && i === 0} />
          </div>
        </div>
      ))}
      <div className="absolute inset-0 -z-10 bg-[linear-gradient(0deg,var(--color-night)_6%,rgb(7_7_10/0.35)_55%,rgb(7_7_10/0.55)_100%)]" aria-hidden />

      <div className="mx-auto flex w-full max-w-7xl flex-1 flex-col px-4 pt-[calc(var(--chrome-h)+1.5rem)] sm:px-6 lg:px-8">
        {count > 1 && (
          <div className="flex items-center gap-2" {...hoverPause}>
            {slides.map((slide, i) => (
              <button key={slide.image.url + i} type="button" onClick={() => go(i)} aria-label={t.slide(i + 1, count)} aria-current={i === index ? "true" : undefined}
                className="group h-6 flex-1 py-2.5">
                <span className="block h-0.5 overflow-hidden rounded-full bg-ink/25">
                  <span className={cn("block h-full bg-[var(--accent-ink)]", i < index && "w-full", i > index && "w-0", i === index && (reducedMotion ? "w-full" : "cinema-progress"))}
                    style={i === index && !reducedMotion ? { animationDuration: `${DURATION}ms`, animationPlayState: running ? "running" : "paused" } : undefined} key={`${i}-${index}`} />
                </span>
              </button>
            ))}
            <button type="button" onClick={() => setPaused((p) => !p)} aria-label={paused ? t.resume : t.pause}
              className="ml-2 grid size-9 place-items-center rounded-full border border-ink/25 text-ink transition hover:border-[var(--accent-ink)]">
              {paused ? <Play className="size-4" aria-hidden /> : <Pause className="size-4" aria-hidden />}
            </button>
          </div>
        )}

        <div className="flex flex-1 flex-col justify-end pb-10 sm:pb-14">
          <div className="self-start" {...hoverPause}>
          {count === 0 ? (
            <Heading className="display max-w-4xl text-balance text-[clamp(2.6rem,7vw,6.5rem)]"><Emphasis text={data.title} /></Heading>
          ) : (
            slides.map((slide, i) => {
              const SlideHeading = i === 0 ? Heading : "h2";
              return (
                // Les titres des autres diapositives restent lisibles par les lecteurs d'écran (le h1 de la page ne disparaît jamais).
                <div key={slide.image.url + i} className={cn("max-w-4xl", i === index ? "cinema-caption" : "sr-only")}>
                  <p className="cartel mb-4 text-[var(--accent-ink)]">
                    {count > 1 && <span className="tabular-nums">{String(i + 1).padStart(2, "0")} / {String(count).padStart(2, "0")}</span>}
                    {slide.eyebrow && <span>{count > 1 && " · "}{slide.eyebrow}</span>}
                  </p>
                  <SlideHeading className="display text-balance text-[clamp(2.4rem,6.5vw,6rem)] drop-shadow-[0_2px_24px_rgb(0_0_0/0.5)]">
                    {frenchSpacing(slide.title || (i === 0 ? data.title : ""))}
                  </SlideHeading>
                  {slide.link && (
                    <LocaleLink href={slide.link.url} tabIndex={i === index ? undefined : -1} className="mt-6 inline-flex items-center gap-2 font-semibold text-ink underline-offset-4 hover:underline">
                      {slide.link.label || common.discover} <ArrowRight className="size-4" aria-hidden />
                    </LocaleLink>
                  )}
                </div>
              );
            })
          )}
          {data.subtitle && <p className="mt-5 max-w-2xl text-lg text-ink/85">{data.subtitle}</p>}
          </div>
        </div>
      </div>

      {(facts.length > 0 || buttons.length > 0) && (
        <div className="border-t border-line bg-night/70 backdrop-blur-md">
          <div className="mx-auto flex w-full max-w-7xl flex-col gap-5 px-4 py-5 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
            {facts.length > 0 && (
              <dl className="grid grid-cols-2 gap-x-8 gap-y-4 sm:flex sm:flex-wrap sm:gap-x-12">
                {facts.map((fact) => (
                  <div key={fact.value + fact.label} className="flex flex-col-reverse">
                    <dt className="text-sm text-ink/75">{fact.label}</dt>
                    <dd className="display text-3xl text-[var(--accent-ink)]">{fact.value}</dd>
                  </div>
                ))}
              </dl>
            )}
            {buttons.length > 0 && (
              <div className="flex flex-wrap gap-3">
                {buttons.map((button) => (
                  <ButtonLink key={button.url + button.label} href={button.url} size="lg" variant={button.style === "secondary" ? "secondary" : "primary"}>{button.label}</ButtonLink>
                ))}
              </div>
            )}
          </div>
        </div>
      )}
    </section>
  );
}
