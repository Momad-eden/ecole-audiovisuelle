"use client";

import { useRef } from "react";
import { Volume2, VolumeX } from "lucide-react";
import { useT } from "@/components/i18n/LocaleProvider";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { accentVars } from "@/lib/contrast";
import { cn, frenchSpacing } from "@/lib/utils";
import { useArtSound } from "./art/useArtSound";
import { useLightRibbons } from "./art/useLightRibbons";
import type { HeroData } from "./types";

/**
 * « Œuvre » : un héros conçu comme une pièce d'exposition. Des rubans de lumière, comme des
 * ondes sonores devenues lumière, se déforment vers la main du visiteur ; le titre est rempli
 * par la photo (ou par la lumière) ; un cartel de musée indique la « fréquence » sous le pointeur.
 * Le son est facultatif, lancé par un bouton, jamais automatiquement.
 */
export function ArtHero({ data, first }: { data: HeroData; first: boolean }) {
  const sectionRef = useRef<HTMLElement>(null);
  const canvasRef = useRef<HTMLCanvasElement>(null);
  const readoutRef = useRef<HTMLSpanElement>(null);
  const { pointer, follow: trace } = useLightRibbons(sectionRef, canvasRef);
  const { listening, loading, toggle: toggleSound, steer } = useArtSound(data.sound);
  const Heading = first ? "h1" : "h2";
  const t = useT().hero;

  function follow(event: React.PointerEvent<HTMLElement>) {
    const { x, y } = trace(event);
    const frequency = Math.round(110 * Math.pow(2, x * 3));
    if (readoutRef.current) readoutRef.current.textContent = `${frequency} Hz · x ${x.toFixed(2)} · y ${y.toFixed(2)}`;
    steer(x, y, pointer.current.energy);
  }

  const image = data.image?.url;

  return (
    <section
      ref={sectionRef}
      onPointerMove={follow}
      className="art-hero relative isolate flex min-h-[92svh] flex-col overflow-hidden bg-night"
      style={accentVars(data.accent)}
    >
      <canvas ref={canvasRef} className="absolute inset-0 -z-10 h-full w-full" aria-hidden />
      <div className="absolute inset-0 -z-10 bg-[radial-gradient(70%_60%_at_50%_45%,transparent,var(--color-night)_92%)]" aria-hidden />

      {/* Cadre d'exposition : filet et repères d'angle. */}
      <div className="pointer-events-none absolute inset-4 border border-ink/15 sm:inset-6" aria-hidden>
        {["left-0 top-0 border-l-2 border-t-2", "right-0 top-0 border-r-2 border-t-2", "bottom-0 left-0 border-b-2 border-l-2", "bottom-0 right-0 border-b-2 border-r-2"].map((corner) => (
          <span key={corner} className={cn("absolute size-5 border-brand", corner)} />
        ))}
      </div>

      <div className="mx-auto flex w-full max-w-7xl flex-1 flex-col justify-end px-8 pb-12 pt-32 sm:px-12 sm:pb-16 lg:px-16">
        {data.eyebrow && <p className="cartel mb-6 text-ink/80">{data.eyebrow}</p>}
        <Heading
          className={cn("art-title display max-w-6xl text-balance", data.title.length <= 26 ? "text-[clamp(3.2rem,11vw,10.5rem)]" : "text-[clamp(2.6rem,7.5vw,7rem)]")}
          style={image ? { backgroundImage: `url("${image}")` } : undefined}
          data-image={image ? "true" : undefined}
        >
          {frenchSpacing(data.title)}
        </Heading>

        <div className="mt-10 grid gap-10 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
          <div>
            {data.subtitle && <p className="max-w-xl text-lg text-ink/85 sm:text-xl">{data.subtitle}</p>}
            <div className="mt-8 flex flex-wrap items-center gap-3">
              {(data.buttons ?? []).map((button) => (
                <ButtonLink key={button.url + button.label} href={button.url} size="lg" variant={button.style === "secondary" ? "secondary" : "primary"}>{button.label}</ButtonLink>
              ))}
              <button type="button" onClick={() => void toggleSound()} aria-pressed={listening} disabled={loading} aria-busy={loading}
                className="inline-flex min-h-14 items-center gap-2 rounded-full border border-ink/25 px-6 text-sm font-semibold text-ink transition hover:border-brand hover:text-brand">
                {listening ? <VolumeX className="size-5" aria-hidden /> : <Volume2 className="size-5" aria-hidden />}
                {loading ? t.loadingSound : listening ? t.mute : t.listen}
              </button>
            </div>
          </div>

          {/* Cartel de musée : l'œuvre, sa technique, et ce que « joue » la main du visiteur. */}
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
