"use client";

import { Pause, Play } from "lucide-react";
import { cn } from "@/lib/utils";
import { useAudio, type Track } from "./AudioProvider";

export function PlayButton({ track, className, label, iconOnly }: { track: Track; className?: string; label?: string; iconOnly?: boolean }) {
  const { track: current, playing, play, toggle } = useAudio();
  const isCurrent = current?.src === track.src;
  const isPlaying = isCurrent && playing;

  return (
    <button
      type="button"
      onClick={() => (isCurrent ? toggle() : play(track))}
      aria-label={isPlaying ? `Mettre en pause « ${track.title} »` : `Écouter « ${track.title} »`}
      className={cn(
        "inline-flex items-center rounded-full bg-[var(--accent-ink)] font-medium text-on-accent transition hover:brightness-110",
        iconOnly ? "size-12 shrink-0 justify-center shadow-[0_0_30px_-8px_var(--accent)]" : "gap-3 px-5 py-3",
        className,
      )}
    >
      {isPlaying ? <Pause className={iconOnly ? "size-6" : "size-5"} aria-hidden /> : <Play className={cn(iconOnly ? "size-6 translate-x-0.5" : "size-5")} aria-hidden />}
      {!iconOnly && (label ?? (isPlaying ? "Pause" : "Écouter"))}
    </button>
  );
}
