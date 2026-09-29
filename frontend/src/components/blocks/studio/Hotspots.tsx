"use client";

import { useEffect, useRef, useState } from "react";
import { cn } from "@/lib/utils";
import type { Hotspot } from "../types";

/**
 * Points lumineux posés sur le matériel de la photo (réglés dans l'admin, en % de la photo).
 * Un point s'ouvre au survol, au focus clavier ou au toucher ; un seul à la fois, Échap le referme.
 * Sur petit écran, le texte recouvre la photo : les points sont masqués et `HotspotList` les liste.
 * La photo est recadrée pour couvrir le héros (object-cover) : les points sont posés sur la zone
 * qu'occupe la photo entière, pour rester sur le matériel quel que soit le format de l'écran.
 */
export function Hotspots({ points, src }: { points: Hotspot[]; src: string }) {
  const [open, setOpen] = useState<number | null>(null);
  const rootRef = useRef<HTMLDivElement>(null);
  const box = useCoverBox(rootRef, src);

  useEffect(() => {
    if (open === null) return;
    const close = (event: KeyboardEvent) => event.key === "Escape" && setOpen(null);
    window.addEventListener("keydown", close);
    return () => window.removeEventListener("keydown", close);
  }, [open]);

  return (
    <div ref={rootRef} className="pointer-events-none absolute inset-0 z-10 hidden overflow-hidden sm:block">
      <div className="absolute" style={box ?? { inset: 0 }}>
      {points.map((point, index) => {
        const isOpen = open === index;
        const toLeft = point.x > 62;
        return (
          <div key={`${point.x}-${point.y}-${index}`} className="pointer-events-auto absolute -translate-x-1/2 -translate-y-1/2" style={{ left: `${point.x}%`, top: `${point.y}%` }}
            onMouseEnter={() => setOpen(index)} onMouseLeave={() => setOpen((current) => (current === index ? null : current))}>
            <button
              type="button"
              aria-label={point.label}
              aria-expanded={isOpen}
              onClick={() => setOpen(isOpen ? null : index)}
              onFocus={() => setOpen(index)}
              onBlur={() => setOpen((current) => (current === index ? null : current))}
              className="hotspot-dot grid size-7 place-items-center rounded-full bg-[var(--accent-ink)] text-[0.7rem] font-bold text-on-accent sm:size-4 sm:text-[0]"
            >
              <span className="sm:sr-only" aria-hidden>{index + 1}</span>
            </button>
            <span
              role="tooltip"
              className={cn(
                "absolute top-1/2 hidden -translate-y-1/2 whitespace-nowrap rounded-md border border-line bg-night/85 px-3 py-1.5 text-sm text-ink shadow-xl backdrop-blur-md transition sm:block",
                toLeft ? "right-full mr-3" : "left-full ml-3",
                isOpen ? "opacity-100" : "pointer-events-none opacity-0",
              )}
            >
              {point.label}
            </span>
          </div>
        );
      })}
      </div>
    </div>
  );
}

/** Zone (en px, dans le conteneur) qu'occupe la photo entière quand elle le couvre en object-cover. */
function useCoverBox(rootRef: React.RefObject<HTMLDivElement | null>, src: string) {
  const [ratio, setRatio] = useState<number | null>(null);
  const [box, setBox] = useState<{ left: number; top: number; width: number; height: number } | null>(null);

  useEffect(() => {
    const image = new window.Image();
    image.onload = () => image.naturalWidth && setRatio(image.naturalWidth / image.naturalHeight);
    image.src = src;
  }, [src]);

  useEffect(() => {
    const root = rootRef.current;
    if (!root || !ratio) return;
    const measure = () => {
      const { width: w, height: h } = root.getBoundingClientRect();
      const width = Math.max(w, h * ratio);
      const height = width / ratio;
      setBox({ left: (w - width) / 2, top: (h - height) / 2, width, height });
    };
    measure();
    const observer = new ResizeObserver(measure);
    observer.observe(root);
    return () => observer.disconnect();
  }, [rootRef, ratio]);

  return box;
}

/** Libellés des points en liste numérotée, pour les petits écrans où les étiquettes ne tiennent pas. */
export function HotspotList({ points }: { points: Hotspot[] }) {
  return (
    <ol className="mt-6 grid gap-2 text-sm text-ink/85 sm:hidden" aria-label="Matériel du studio">
      {points.map((point, index) => (
        <li key={`${point.label}-${index}`} className="flex items-center gap-3">
          <span className="grid size-6 shrink-0 place-items-center rounded-full bg-[var(--accent-ink)] text-xs font-bold text-on-accent" aria-hidden>{index + 1}</span>
          {point.label}
        </li>
      ))}
    </ol>
  );
}
