"use client";

import { Pause, Play } from "lucide-react";
import { cn } from "@/lib/utils";
import { useAudio, type Track } from "./AudioProvider";

export function PlayButton({ track, className, label }: { track: Track; className?: string; label?: string }) {
  const { track: current, playing, play, toggle } = useAudio();
  const isCurrent = current?.src === track.src;
  const isPlaying = isCurrent && playing;

  return (
    <button
      type="button"
      onClick={() => (isCurrent ? toggle() : play(track))}
      aria-label={isPlaying ? `Mettre en pause « ${track.title} »` : `Écouter « ${track.title} »`}
      className={cn("inline-flex items-center gap-3 rounded-full bg-[var(--accent)] px-5 py-3 font-medium text-night transition hover:brightness-110", className)}
    >
      {isPlaying ? <Pause className="size-5" aria-hidden /> : <Play className="size-5" aria-hidden />}
      {label ?? (isPlaying ? "Pause" : "Écouter")}
    </button>
  );
}
