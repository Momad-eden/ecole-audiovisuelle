"use client";

import { useRef } from "react";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { MediaImage } from "@/components/ui/MediaImage";
import { InView } from "@/components/motion/InView";
import { accentVars } from "@/lib/contrast";
import { splitHighlight } from "@/lib/highlight";
import { cn, frenchSpacing } from "@/lib/utils";
import { ConsoleArt } from "./HeroArt";
import { HotspotList, Hotspots } from "./studio/Hotspots";
import { StudioPlayer } from "./studio/StudioPlayer";
import { useRecTimecode } from "./studio/useRecTimecode";
import type { HeroData } from "./types";

/**
 * Héros « Studio » : la photo du studio en grand, le matériel commenté par des points posés dans
 * l'admin, un voyant REC et un lecteur pour écouter jusqu'à trois productions. Sans photo, la
 * console dessinée prend le relais.
 */
export function StudioHero({ data, first }: { data: HeroData; first: boolean }) {
  const sectionRef = useRef<HTMLElement>(null);
  const timecodeRef = useRef<HTMLSpanElement>(null);
  useRecTimecode(sectionRef, timecodeRef);
  const Heading = first ? "h1" : "h2";
  const title = frenchSpacing(data.title);
  const parts = splitHighlight(title, data.highlight);
  const points = data.image ? (data.hotspots ?? []) : [];
  const tracks = data.tracks ?? [];

  return (
    <section
      ref={sectionRef}
      data-testid="studio-hero"
      data-first={first ? "" : undefined}
      className="scene-dark relative isolate flex min-h-[92svh] flex-col justify-end overflow-hidden bg-night pb-6 pt-28 sm:pb-8"
      style={accentVars(data.accent || "#ff3b30")}
    >
      <div className="absolute inset-0 -z-10" aria-hidden>
        {data.image ? (
          <div className="absolute inset-0 brightness-[0.68]">
            <MediaImage image={data.image} sizes="100vw" priority={first} />
          </div>
        ) : (
          <InView className="absolute inset-0 opacity-50"><ConsoleArt /></InView>
        )}
        <div className="absolute inset-0 bg-[linear-gradient(90deg,var(--color-night)_0%,rgb(7_7_10/0.78)_34%,transparent_64%),linear-gradient(0deg,var(--color-night)_0%,transparent_34%)]" />
      </div>

      {data.image && points.length > 0 && <Hotspots points={points} src={data.image.url} />}

      <div className="pointer-events-none relative z-20 mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
        <div className="pointer-events-auto max-w-xl">
          <p className="cartel mb-5 flex items-center gap-2 text-rec">
            <span className="rec-dot size-2 rounded-full bg-rec" aria-hidden />
            REC <span ref={timecodeRef} className="tabular-nums" aria-hidden>00:00:00:00</span>
            {data.eyebrow && <span className="ml-2 text-ink/75">· {data.eyebrow}</span>}
          </p>
          <Heading className={cn("display text-balance drop-shadow-[0_2px_24px_rgb(0_0_0/0.5)]", title.length <= 30 ? "text-[clamp(2.6rem,7.5vw,6.5rem)]" : "text-[clamp(2.2rem,5.5vw,4.8rem)]")}>
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
          {data.subtitle && <p className="mt-5 max-w-lg text-lg text-ink/85">{data.subtitle}</p>}
          {data.buttons && data.buttons.length > 0 && (
            <div className="mt-8 flex flex-wrap gap-3">
              {data.buttons.map((button) => (
                <ButtonLink key={button.url + button.label} href={button.url} size="lg" variant={button.style === "secondary" ? "secondary" : "primary"}>{button.label}</ButtonLink>
              ))}
            </div>
          )}
          {points.length > 0 && <HotspotList points={points} />}
        </div>

        {tracks.length > 0 && (
          <div className="pointer-events-auto mt-10 sm:mt-12">
            <StudioPlayer tracks={tracks} accent={data.accent} />
          </div>
        )}
      </div>
    </section>
  );
}
