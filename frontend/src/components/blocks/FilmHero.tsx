"use client";

import { useEffect, useRef, useState } from "react";
import { ArrowDown, Pause, Play, X } from "lucide-react";
import { useT } from "@/components/i18n/LocaleProvider";
import { useReducedMotion } from "@/components/motion/useReducedMotion";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { MediaImage } from "@/components/ui/MediaImage";
import { accentVars } from "@/lib/contrast";
import { cn, frenchSpacing, videoEmbed } from "@/lib/utils";
import type { HeroData } from "./types";

/** Durée d'une photo quand le film défile en images (sans vidéo), en millisecondes. */
const STILL = 7000;

/**
 * « Film » : ouverture plein écran pour l'accueil. Une boucle vidéo muette (ou, sans vidéo, les photos qui se
 * succèdent en fondu avec un lent zoom), une grande phrase qui se dévoile mot à mot, deux boutons, et « Voir le
 * film » qui ouvre la version complète avec le son. La boucle se met en pause (bouton, critère WCAG 2.2.2) ;
 * rien ne bouge si l'internaute limite les animations.
 */
export function FilmHero({ data, first }: { data: HeroData; first: boolean }) {
  const t = useT().film;
  const reducedMotion = useReducedMotion();
  const videoRef = useRef<HTMLVideoElement>(null);
  const dialogRef = useRef<HTMLDialogElement>(null);
  const [paused, setPaused] = useState(false);
  const [filmOpen, setFilmOpen] = useState(false);
  const [index, setIndex] = useState(0);
  const Heading = first ? "h1" : "h2";

  const stills = data.images?.length ? data.images : data.image ? [data.image] : [];
  const video = data.videoLoop && !reducedMotion ? data.videoLoop : null;
  const film = data.filmUrl ? videoEmbed(data.filmUrl) : null;
  const moving = !reducedMotion && !paused;
  const slideshow = !video && stills.length > 1;

  useEffect(() => {
    if (!slideshow || !moving) return;
    const timer = window.setTimeout(() => setIndex((i) => (i + 1) % stills.length), STILL);
    return () => window.clearTimeout(timer);
  }, [slideshow, moving, index, stills.length]);

  useEffect(() => {
    const element = videoRef.current;
    if (!element) return;
    if (paused || filmOpen) element.pause();
    else void element.play().catch(() => {});
  }, [paused, filmOpen]);

  useEffect(() => {
    const dialog = dialogRef.current;
    if (!dialog) return;
    if (filmOpen && !dialog.open) dialog.showModal();
    if (!filmOpen && dialog.open) dialog.close();
  }, [filmOpen]);

  const words = frenchSpacing(data.title).split(" ");

  return (
    <section
      data-testid="film-hero"
      data-first={first ? "" : undefined}
      className="scene-dark relative isolate flex min-h-[100svh] flex-col justify-end overflow-hidden bg-night"
      style={accentVars(data.accent)}
    >
      <div className="absolute inset-0 -z-20" aria-hidden>
        {video ? (
          <video ref={videoRef} className="h-full w-full object-cover" src={video} poster={stills[0]?.url} autoPlay muted loop playsInline preload="metadata" />
        ) : (
          stills.map((image, i) => (
            <div key={image.url + i} className={cn("absolute inset-0 transition-opacity duration-[1600ms]", i === index ? "opacity-100" : "opacity-0")}>
              <div className={cn("absolute inset-0", i === index && !reducedMotion && "film-zoom")} style={{ animationPlayState: moving ? "running" : "paused" }}>
                <MediaImage image={{ ...image, alt: "" }} sizes="100vw" priority={first && i === 0} />
              </div>
            </div>
          ))
        )}
      </div>
      {/* Voile : la photo reste vivante en haut, le texte se pose sur un fond presque noir. */}
      <div className="absolute inset-0 -z-10 bg-[linear-gradient(180deg,rgb(7_7_10/0.55)_0%,rgb(7_7_10/0.15)_35%,rgb(7_7_10/0.55)_65%,var(--color-night)_100%)]" aria-hidden />
      <div className="absolute inset-0 -z-10 bg-[radial-gradient(70%_60%_at_15%_85%,color-mix(in_oklab,var(--accent)_22%,transparent),transparent_70%)]" aria-hidden />

      <div className="mx-auto w-full max-w-7xl px-4 pb-14 pt-40 sm:px-6 sm:pb-20 lg:px-8">
        {data.eyebrow && (
          <p className="film-rise cartel mb-6 flex items-center gap-3 text-ink/85" style={{ animationDelay: "100ms" }}>
            <span className="relative flex size-2" aria-hidden>
              <span className="absolute inline-flex size-full rounded-full bg-[var(--accent)] opacity-60 motion-safe:animate-ping" />
              <span className="relative inline-flex size-2 rounded-full bg-[var(--accent)]" />
            </span>
            {data.eyebrow}
          </p>
        )}
        <Heading className="display max-w-6xl text-balance text-[clamp(2.6rem,6.4vw,6.6rem)] uppercase leading-[0.95]">
          {words.map((word, i) => (
            <span key={i}>
              <span className="inline-block overflow-hidden pb-[0.06em] align-bottom">
                <span className="film-word inline-block" style={{ animationDelay: `${180 + i * 70}ms` }}>{word}</span>
              </span>
              {i < words.length - 1 && " "}
            </span>
          ))}
        </Heading>
        {data.subtitle && (
          <p className="film-rise mt-7 max-w-2xl text-lg text-ink/85 sm:text-xl" style={{ animationDelay: `${300 + words.length * 70}ms` }}>
            {frenchSpacing(data.subtitle)}
          </p>
        )}

        <div className="film-rise mt-10 flex flex-wrap items-center gap-3" style={{ animationDelay: `${420 + words.length * 70}ms` }}>
          {(data.buttons ?? []).map((button) => (
            <ButtonLink key={button.url + button.label} href={button.url} size="lg" variant={button.style === "secondary" ? "secondary" : "primary"}>{button.label}</ButtonLink>
          ))}
          {film && (
            <button type="button" onClick={() => setFilmOpen(true)} className="group inline-flex min-h-12 items-center gap-3 rounded-full py-1.5 pl-1.5 pr-5 text-sm font-semibold text-ink transition hover:bg-ink/10">
              <span className="grid size-10 place-items-center rounded-full bg-ink text-night transition group-hover:scale-105">
                <Play className="size-4 translate-x-px" aria-hidden />
              </span>
              {t.watch}
            </button>
          )}
        </div>

        <div className="mt-14 flex items-center justify-between gap-4 border-t border-ink/15 pt-5">
          <a href="#apres-film" className="group inline-flex items-center gap-3 text-sm text-ink/75 transition hover:text-ink">
            <span className="grid size-9 place-items-center rounded-full border border-ink/25 transition group-hover:border-ink/60">
              <ArrowDown className="size-4 motion-safe:animate-bounce" aria-hidden />
            </span>
            {t.scroll}
          </a>
          <div className="flex items-center gap-3">
            {slideshow && (
              <div className="hidden items-center gap-1.5 sm:flex" aria-hidden>
                {stills.map((image, i) => (
                  <span key={image.url + i} className={cn("h-0.5 rounded-full transition-all duration-500", i === index ? "w-8 bg-ink" : "w-3 bg-ink/30")} />
                ))}
              </div>
            )}
            {(video || slideshow) && !reducedMotion && (
              <button type="button" onClick={() => setPaused((p) => !p)} aria-label={paused ? t.play : t.pause}
                className="grid size-9 place-items-center rounded-full border border-ink/25 text-ink transition hover:border-ink/60">
                {paused ? <Play className="size-4" aria-hidden /> : <Pause className="size-4" aria-hidden />}
              </button>
            )}
          </div>
        </div>
      </div>
      <span id="apres-film" className="absolute bottom-0" aria-hidden />

      {film && (
        <dialog ref={dialogRef} onClose={() => setFilmOpen(false)} aria-label={data.title}
          className="m-auto w-[min(72rem,calc(100vw-2rem))] max-w-none overflow-visible bg-transparent p-0 backdrop:bg-night/90 backdrop:backdrop-blur-sm">
          <button type="button" onClick={() => setFilmOpen(false)} aria-label={t.close}
            className="absolute -top-14 right-0 grid size-11 place-items-center rounded-full border border-ink/30 text-ink hover:bg-ink/10">
            <X className="size-5" aria-hidden />
          </button>
          <div className="aspect-video overflow-hidden rounded-2xl bg-night-3 shadow-2xl">
            {filmOpen && (
              <iframe src={film.embedUrl} title={data.title} allow="autoplay; encrypted-media; picture-in-picture; fullscreen" allowFullScreen className="h-full w-full" />
            )}
          </div>
        </dialog>
      )}
    </section>
  );
}
