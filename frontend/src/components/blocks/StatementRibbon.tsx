"use client";

import { useState } from "react";
import { Pause, Play } from "lucide-react";
import { useT } from "@/components/i18n/LocaleProvider";
import { MediaImage } from "@/components/ui/MediaImage";
import type { Image } from "@/lib/types";
import { cn } from "@/lib/utils";

/**
 * Bandeau de photos du Manifeste, façon planche éditoriale : deux rangées qui glissent en sens opposé, à des
 * vitesses différentes, formats alternés (paysage / portrait), photos numérotées, légèrement désaturées et
 * qui reprennent leurs couleurs au survol. Arrêt au survol des photos et par un bouton visible ; rangées
 * immobiles (défilables à la main) si le visiteur limite les animations.
 */
export function StatementRibbon({ images }: { images: Image[] }) {
  const t = useT().statement;
  const [paused, setPaused] = useState(false);
  // Photos réparties en alternance (deux photos voisines ne se suivent pas), chaque rangée répétée jusqu'à
  // 8 photos au moins : une moitié de piste couvre toujours l'écran, la boucle ne laisse jamais de vide.
  const split = images.length >= 4 ? [images.filter((_, i) => i % 2 === 0), images.filter((_, i) => i % 2 === 1)] : [images, [...images].reverse()];
  const rows = split.map((row) => Array.from({ length: Math.max(row.length, Math.ceil(8 / row.length) * row.length) }, (_, i) => ({ image: row[i % row.length], number: images.indexOf(row[i % row.length]) + 1 })));

  return (
    <div className={cn("statement-ribbon group/ribbon relative mt-14 sm:mt-20", paused && "is-paused")}>
      <button
        type="button"
        onClick={() => setPaused((p) => !p)}
        aria-label={paused ? t.play : t.pause}
        className="statement-ribbon-toggle absolute right-4 top-0 z-10 grid size-10 -translate-y-1/2 place-items-center rounded-full border border-line bg-night/90 text-ink shadow-lg backdrop-blur transition hover:border-[var(--accent)] sm:right-8"
      >
        {paused ? <Play className="size-4" aria-hidden /> : <Pause className="size-4" aria-hidden />}
      </button>
      <div className="grid gap-3 sm:gap-4" aria-hidden>
        {rows.map((row, r) => (
          <div key={r} className="statement-row">
            <div className={cn("statement-track", r === 1 && "statement-track-reverse")}>
              {[...row, ...row].map(({ image, number }, index) => {
                const portrait = (index + r) % 3 === 1;
                return (
                  <figure
                    key={image.url + index}
                    className={cn(
                      "group/photo relative shrink-0 overflow-hidden rounded-2xl border border-line bg-night-3",
                      r === 0 ? "h-44 sm:h-64" : "h-36 sm:h-52",
                      portrait ? (r === 0 ? "w-32 sm:w-48" : "w-28 sm:w-40") : (r === 0 ? "w-64 sm:w-[24rem]" : "w-52 sm:w-80"),
                    )}
                  >
                    <MediaImage image={{ ...image, alt: "" }} sizes="384px" className="saturate-[0.55] transition duration-700 group-hover/photo:scale-105 group-hover/photo:saturate-100" />
                    <span className="cartel absolute bottom-3 left-3 rounded-full bg-black/45 px-2 py-0.5 text-white/85 backdrop-blur-sm">
                      {String(number).padStart(2, "0")}
                    </span>
                  </figure>
                );
              })}
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
