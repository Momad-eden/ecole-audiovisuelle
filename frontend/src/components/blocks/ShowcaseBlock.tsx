"use client";

import { useEffect, useRef, useState } from "react";
import { ArrowRight, ChevronLeft, ChevronRight, Expand, Play, X } from "lucide-react";
import { LocaleLink } from "@/components/i18n/LocaleLink";
import { useT } from "@/components/i18n/LocaleProvider";
import { useReducedMotion } from "@/components/motion/useReducedMotion";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { MediaImage } from "@/components/ui/MediaImage";
import { cn, frenchSpacing } from "@/lib/utils";
import type { ShowcaseData, ShowcaseItem } from "./types";

/** Les quatre premières cases forment un bloc de deux rangées (une grande, une haute, deux petites). */
const LEAD = ["col-span-2 row-span-2", "", "lg:row-span-2", ""];
/** La dernière case s'élargit pour finir la rangée (ordinateur : 4 colonnes). */
const FILL = ["", "", "lg:col-span-2", "lg:col-span-3", "lg:col-span-4"];

function spanOf(index: number, count: number): string {
  if (index < LEAD.length) return LEAD[index];
  const rest = (count - LEAD.length) % 4;
  return index === count - 1 && rest > 0 ? FILL[4 - rest + 1] : "";
}

/**
 * « Vitrine photos et vidéos » : une grille au rythme varié ; une vidéo joue en boucle, sans le son, quand elle
 * est à l'écran. Un clic ouvre la visionneuse plein écran (flèches, clavier, Échap), où la vidéo a le son.
 */
export function ShowcaseBlock({ data }: { data: ShowcaseData }) {
  const t = useT().showcase;
  const items = data.items ?? [];
  const [open, setOpen] = useState<number | null>(null);
  const dialogRef = useRef<HTMLDialogElement>(null);

  useEffect(() => {
    const dialog = dialogRef.current;
    if (!dialog) return;
    if (open !== null && !dialog.open) dialog.showModal();
    if (open === null && dialog.open) dialog.close();
  }, [open]);

  if (items.length === 0) return null;
  const go = (step: number) => setOpen((i) => (i === null ? i : (i + step + items.length) % items.length));
  const current = open === null ? null : items[open];

  return (
    <section data-testid="showcase" className="relative py-20 sm:py-28">
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div className="mb-10 flex flex-wrap items-end justify-between gap-6 sm:mb-14">
          <div className="max-w-3xl">
            {data.eyebrow && <p className="cartel mb-4 flex items-center gap-3 text-[var(--accent-ink)]"><span className="h-px w-10 bg-[var(--accent-ink)]" aria-hidden />{data.eyebrow}</p>}
            <h2 className="display text-balance text-[clamp(2.2rem,5vw,4.2rem)]">{frenchSpacing(data.title)}</h2>
            {data.text && <p className="mt-5 max-w-2xl text-lg text-ink/80">{frenchSpacing(data.text)}</p>}
          </div>
          {data.buttonLabel && data.buttonUrl && <ButtonLink href={data.buttonUrl} variant="secondary">{data.buttonLabel}</ButtonLink>}
        </div>

        <ul className="grid auto-rows-[14rem] grid-cols-2 gap-3 sm:auto-rows-[16rem] sm:gap-4 lg:grid-cols-4">
          {items.map((item, index) => (
            <li key={item.image.url + index} className={cn("relative", spanOf(index, items.length))}>
              <Tile item={item} index={index} onOpen={() => setOpen(index)} label={t.open(item.caption || t.item(index + 1))} videoLabel={t.video} />
            </li>
          ))}
        </ul>
      </div>

      <dialog ref={dialogRef} onClose={() => setOpen(null)} aria-label={data.title}
        onKeyDown={(event) => { if (event.key === "ArrowRight") go(1); if (event.key === "ArrowLeft") go(-1); }}
        className="m-0 h-dvh max-h-none w-full max-w-none bg-night/95 p-0 text-ink backdrop:bg-night/90 backdrop:backdrop-blur-sm">
        {current && open !== null && (
          <div className="flex h-full flex-col">
            <div className="flex items-center justify-between gap-4 px-4 py-3 sm:px-6">
              <p className="cartel tabular-nums" aria-live="polite">{t.counter(open + 1, items.length)}</p>
              <button type="button" onClick={() => setOpen(null)} aria-label={t.close} className="grid size-11 place-items-center rounded-full border border-line hover:bg-ink/10">
                <X className="size-5" aria-hidden />
              </button>
            </div>
            <div className="relative min-h-0 flex-1 px-4 sm:px-20">
              {current.video ? (
                <video key={current.video} src={current.video} poster={current.image.url} controls autoPlay playsInline className="h-full w-full object-contain" />
              ) : (
                <div className="relative h-full w-full">
                  <MediaImage key={current.image.url} image={current.image} sizes="100vw" fit="contain" />
                </div>
              )}
              {items.length > 1 && (
                <>
                  <button type="button" onClick={() => go(-1)} aria-label={t.previous} className="absolute left-2 top-1/2 grid size-12 -translate-y-1/2 place-items-center rounded-full border border-line bg-night/70 hover:bg-ink/10 sm:left-5">
                    <ChevronLeft className="size-5" aria-hidden />
                  </button>
                  <button type="button" onClick={() => go(1)} aria-label={t.next} className="absolute right-2 top-1/2 grid size-12 -translate-y-1/2 place-items-center rounded-full border border-line bg-night/70 hover:bg-ink/10 sm:right-5">
                    <ChevronRight className="size-5" aria-hidden />
                  </button>
                </>
              )}
            </div>
            <div className="flex min-h-16 flex-wrap items-center justify-between gap-3 px-4 py-4 sm:px-6">
              <p className="text-ink/85">{current.caption}</p>
              {current.url && (
                <LocaleLink href={current.url} onClick={() => setOpen(null)} className="group inline-flex items-center gap-2 text-sm font-semibold text-[var(--accent-ink)]">
                  {current.caption || t.open("")}
                  <ArrowRight className="size-4 transition-transform group-hover:translate-x-1" aria-hidden />
                </LocaleLink>
              )}
            </div>
          </div>
        )}
      </dialog>
    </section>
  );
}

function Tile({ item, index, onOpen, label, videoLabel }: { item: ShowcaseItem; index: number; onOpen: () => void; label: string; videoLabel: string }) {
  const ref = useRef<HTMLButtonElement>(null);
  const videoRef = useRef<HTMLVideoElement>(null);
  const reducedMotion = useReducedMotion();
  const [visible, setVisible] = useState(false);
  const [seen, setSeen] = useState(false);

  // La vidéo ne se charge et ne joue que lorsqu'elle est à l'écran.
  useEffect(() => {
    const element = ref.current;
    if (!element || !item.video) return;
    const observer = new IntersectionObserver(([entry]) => {
      setVisible(entry.isIntersecting);
      if (entry.isIntersecting) setSeen(true);
    }, { rootMargin: "120px" });
    observer.observe(element);
    return () => observer.disconnect();
  }, [item.video]);

  useEffect(() => {
    const element = videoRef.current;
    if (!element) return;
    if (visible) void element.play().catch(() => {});
    else element.pause();
  }, [visible, seen]);

  return (
    <button ref={ref} type="button" onClick={onOpen} aria-label={label}
      className="group relative block h-full w-full overflow-hidden rounded-3xl border border-line bg-night-3 text-left focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--accent)]">
      <div className="absolute inset-0 transition duration-700 ease-out group-hover:scale-[1.05]">
        <MediaImage image={{ ...item.image, alt: "" }} sizes={index === 0 ? "(min-width: 1024px) 50vw, 100vw" : "(min-width: 1024px) 25vw, 50vw"} />
        {item.video && !reducedMotion && seen && (
          <video ref={videoRef} src={item.video} poster={item.image.url} muted loop playsInline preload="metadata" className="absolute inset-0 h-full w-full object-cover" aria-hidden />
        )}
      </div>
      <div className="absolute inset-0 bg-gradient-to-t from-night/85 via-night/10 to-transparent opacity-80 transition group-hover:opacity-100" aria-hidden />
      {item.video && (
        <span className="absolute left-4 top-4 inline-flex items-center gap-1.5 rounded-full bg-night/70 px-3 py-1.5 text-xs font-semibold backdrop-blur" aria-hidden>
          <Play className="size-3" /> {videoLabel}
        </span>
      )}
      <span className="absolute right-4 top-4 grid size-9 place-items-center rounded-full bg-night/60 opacity-0 backdrop-blur transition group-hover:opacity-100" aria-hidden>
        <Expand className="size-4" />
      </span>
      {item.caption && (
        <span className={cn("absolute inset-x-0 bottom-0 p-4 font-semibold text-ink sm:p-5", index === 0 ? "text-lg sm:text-2xl" : "text-sm sm:text-base")} aria-hidden>
          {item.caption}
        </span>
      )}
    </button>
  );
}
