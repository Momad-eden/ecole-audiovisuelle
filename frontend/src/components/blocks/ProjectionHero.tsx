"use client";

import { plainTitle } from "@/lib/emphasis";
import { useRef } from "react";
import { Volume2, VolumeX } from "lucide-react";
import { useT } from "@/components/i18n/LocaleProvider";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { MediaImage } from "@/components/ui/MediaImage";
import { accentVars } from "@/lib/contrast";
import { splitHighlight } from "@/lib/highlight";
import { cn, frenchSpacing } from "@/lib/utils";
import { useArtSound } from "./art/useArtSound";
import { useLightRibbons } from "./art/useLightRibbons";
import type { HeroData } from "./types";

/**
 * « Projection » : la photo choisie dans l'admin en fond, assombrie, traversée par les rubans de
 * lumière de l'Œuvre d'art ; autour de la main du visiteur, une poursuite lui rend ses couleurs.
 * Titre plein (un mot peut prendre la couleur de lumière), cadre d'exposition sous la navigation,
 * cartel et son facultatif, jamais lancé automatiquement.
 */
export function ProjectionHero({ data, first }: { data: HeroData; first: boolean }) {
  const sectionRef = useRef<HTMLElement>(null);
  const canvasRef = useRef<HTMLCanvasElement>(null);
  const readoutRef = useRef<HTMLSpanElement>(null);
  const { pointer, follow: trace } = useLightRibbons(sectionRef, canvasRef);
  const { listening, loading, toggle, steer } = useArtSound(data.sound);
  const Heading = first ? "h1" : "h2";
  const t = useT().hero;
  const title = frenchSpacing(plainTitle(data.title));
  const parts = splitHighlight(title, data.highlight);

  function follow(event: React.PointerEvent<HTMLElement>) {
    const { x, y } = trace(event);
    // La poursuite suit la main par des variables CSS : aucun rendu React à chaque mouvement.
    sectionRef.current?.style.setProperty("--px", `${(x * 100).toFixed(1)}%`);
    sectionRef.current?.style.setProperty("--py", `${(y * 100).toFixed(1)}%`);
    const frequency = Math.round(110 * Math.pow(2, x * 3));
    if (readoutRef.current) readoutRef.current.textContent = `${frequency} Hz · x ${x.toFixed(2)} · y ${y.toFixed(2)}`;
    steer(x, y, pointer.current.energy);
  }

  return (
    <section
      ref={sectionRef}
      onPointerMove={follow}
      data-testid="projection-hero"
      data-first={first ? "" : undefined}
      className="projection-hero scene-dark relative isolate flex min-h-[92svh] flex-col overflow-hidden bg-night"
      style={accentVars(data.accent)}
    >
      {data.image && (
        <>
          <div className="absolute inset-0 -z-40 brightness-[0.42] saturate-[0.9]" aria-hidden>
            <MediaImage image={data.image} sizes="100vw" priority={first} />
          </div>
          <div className="projection-spot absolute inset-0 -z-30" aria-hidden>
            <MediaImage image={data.image} sizes="100vw" className="saturate-[1.2]" />
          </div>
        </>
      )}
      <div className="absolute inset-0 -z-20 bg-[linear-gradient(0deg,var(--color-night)_4%,transparent_58%),radial-gradient(90%_70%_at_50%_40%,transparent,var(--color-night)_96%)]" aria-hidden />
      <canvas ref={canvasRef} className="absolute inset-0 -z-10 h-full w-full mix-blend-screen" aria-hidden />

      {/* Cadre d'exposition : il commence sous la barre de navigation (h-18), jamais à travers. */}
      <div data-testid="projection-frame" className="pointer-events-none absolute inset-x-4 bottom-4 top-[calc(var(--chrome-h)+0.75rem)] border border-ink/15 sm:inset-x-6 sm:bottom-6 sm:top-[calc(var(--chrome-h)+1.25rem)]" aria-hidden>
        {["left-0 top-0 border-l-2 border-t-2", "right-0 top-0 border-r-2 border-t-2", "bottom-0 left-0 border-b-2 border-l-2", "bottom-0 right-0 border-b-2 border-r-2"].map((corner) => (
          <span key={corner} className={cn("absolute size-5 border-brand", corner)} />
        ))}
      </div>

      <div className="mx-auto flex w-full max-w-7xl flex-1 flex-col justify-end px-8 pb-12 pt-36 sm:px-12 sm:pb-16 lg:px-16">
        {data.eyebrow && <p className="cartel mb-6 text-ink/80">{data.eyebrow}</p>}
        <Heading className={cn("display max-w-6xl text-balance drop-shadow-[0_2px_24px_rgb(0_0_0/0.45)]", title.length <= 26 ? "text-[clamp(3rem,10vw,9.5rem)]" : "text-[clamp(2.4rem,7vw,6.5rem)]")}>
          {parts ? (
            <>
              {parts[0]}
              <span className="text-[var(--accent-ink)]">{parts[1]}</span>
              {parts[2]}
            </>
          ) : (
            title
          )}
        </Heading>

        <div className="mt-10 grid gap-10 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
          <div>
            {data.subtitle && <p className="max-w-xl text-lg text-ink/85 sm:text-xl">{data.subtitle}</p>}
            <div className="mt-8 flex flex-wrap items-center gap-3">
              {(data.buttons ?? []).map((button) => (
                <ButtonLink key={button.url + button.label} href={button.url} size="lg" variant={button.style === "secondary" ? "secondary" : "primary"}>{button.label}</ButtonLink>
              ))}
              <button type="button" onClick={() => void toggle()} aria-pressed={listening} disabled={loading} aria-busy={loading}
                className="inline-flex min-h-14 items-center gap-2 rounded-full border border-ink/25 bg-night/40 px-6 text-sm font-semibold text-ink backdrop-blur-sm transition hover:border-brand hover:text-brand">
                {listening ? <VolumeX className="size-5" aria-hidden /> : <Volume2 className="size-5" aria-hidden />}
                {loading ? t.loadingSound : listening ? t.mute : t.listen}
              </button>
            </div>
          </div>

          <aside className="w-full max-w-xs border-l-2 border-brand bg-night/60 px-5 py-4 backdrop-blur-sm" aria-label={t.cartel}>
            <p className="display text-base">{data.caption || t.defaultCaption}</p>
            <p className="mt-1 text-sm text-ink-muted">{t.credit}</p>
            <p className="cartel mt-3 tabular-nums" aria-hidden><span ref={readoutRef}>220 Hz · x 0.62 · y 0.55</span></p>
          </aside>
        </div>
      </div>
    </section>
  );
}
