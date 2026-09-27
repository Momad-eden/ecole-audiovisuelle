"use client";

import Link from "next/link";
import { Pause, Play, X } from "lucide-react";
import { formatDuration } from "@/lib/utils";
import { useAudio } from "./AudioProvider";
import { Waveform } from "./Waveform";

/** Barre de lecture fixe en bas d'écran, visible dès qu'un son a été lancé. */
export function PlayerBar() {
  const { track, playing, toggle, seek, close, currentTime, duration } = useAudio();
  if (!track) return null;

  const progress = duration ? currentTime / duration : 0;

  return (
    <div
      role="region"
      aria-label="Lecteur audio"
      className="fixed inset-x-0 bottom-0 z-50 border-t border-line bg-night-2/95 backdrop-blur"
      style={{ ["--accent" as string]: track.accent ?? "var(--color-brand)" }}
    >
      <div className="mx-auto flex max-w-7xl items-center gap-4 px-4 py-3 sm:px-6">
        <button
          type="button"
          onClick={toggle}
          aria-label={playing ? "Mettre en pause" : "Lire"}
          className="grid size-11 shrink-0 place-items-center rounded-full bg-[var(--accent)] text-night"
        >
          {playing ? <Pause className="size-5" aria-hidden /> : <Play className="size-5 translate-x-px" aria-hidden />}
        </button>
        <div className="min-w-0 flex-1">
          <div className="flex items-baseline justify-between gap-3">
            <p className="truncate text-sm font-medium">
              {track.href ? <Link href={track.href} className="hover:underline">{track.title}</Link> : track.title}
              {track.subtitle && <span className="ml-2 text-ink-muted">{track.subtitle}</span>}
            </p>
            <span className="cartel shrink-0">
              {formatDuration(currentTime)} / {formatDuration(duration)}
            </span>
          </div>
          <Waveform peaks={track.peaks} progress={progress} onSeek={seek} label={`Position dans « ${track.title} »`} className="h-8" />
        </div>
        <button type="button" onClick={close} aria-label="Fermer le lecteur" className="grid size-9 place-items-center rounded-full text-ink-muted hover:text-ink">
          <X className="size-5" aria-hidden />
        </button>
      </div>
    </div>
  );
}
