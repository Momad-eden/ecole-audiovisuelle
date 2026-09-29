"use client";

import { useState } from "react";
import { Pause, Play } from "lucide-react";
import { useAudio } from "@/components/audio/AudioProvider";
import { usePeaks } from "@/components/audio/peaks";
import { Waveform } from "@/components/audio/Waveform";
import { cn } from "@/lib/utils";
import type { HeroTrack } from "../types";

const time = (seconds: number) => `${Math.floor(seconds / 60)}:${String(Math.floor(seconds % 60)).padStart(2, "0")}`;

/**
 * Lecteur du héros Studio : jusqu'à trois productions choisies dans l'admin. Il passe par le
 * lecteur global du site, donc l'écoute continue dans la barre de lecture quand on fait défiler.
 * Rien ne joue avant que le visiteur appuie sur lecture.
 */
export function StudioPlayer({ tracks, accent }: { tracks: HeroTrack[]; accent?: string | null }) {
  const playable = tracks.filter((track): track is HeroTrack & { url: string } => Boolean(track.url));
  const [selected, setSelected] = useState(0);
  const audio = useAudio();
  const track = playable[Math.min(selected, playable.length - 1)];
  const peaks = usePeaks(track?.url, null, Boolean(track));
  if (!track) return null;

  const isCurrent = audio.track?.src === track.url;
  const isPlaying = isCurrent && audio.playing;
  const progress = isCurrent && audio.duration > 0 ? audio.currentTime / audio.duration : 0;

  const start = () => (isCurrent ? audio.toggle() : audio.play({ src: track.url, title: track.title, subtitle: track.credits, accent }));

  return (
    <div className="rounded-2xl border border-line bg-night/75 p-3 shadow-2xl backdrop-blur-md sm:p-4" data-testid="studio-player">
      <div className="flex items-center gap-3 sm:gap-4">
        <button type="button" onClick={start}
          aria-label={isPlaying ? `Mettre en pause « ${track.title} »` : `Écouter « ${track.title} »`}
          className="grid size-12 shrink-0 place-items-center rounded-full bg-[var(--accent-ink)] text-on-accent shadow-[0_0_30px_-8px_var(--accent)] transition hover:brightness-110">
          {isPlaying ? <Pause className="size-6" aria-hidden /> : <Play className="size-6 translate-x-0.5" aria-hidden />}
        </button>
        <div className="min-w-0 sm:w-48">
          <p className="truncate font-semibold">{track.title}</p>
          {track.credits && <p className="cartel truncate text-ink-muted">{track.credits}</p>}
        </div>
        <Waveform peaks={peaks} progress={progress} onSeek={isCurrent ? (ratio) => audio.seek(Math.min(1, Math.max(0, ratio))) : undefined}
          label={`Progression de « ${track.title} »`} className="hidden flex-1 sm:block" />
        <span className="cartel hidden shrink-0 tabular-nums text-ink-muted md:inline">
          {isCurrent && audio.duration > 0 ? `${time(audio.currentTime)} / ${time(audio.duration)}` : "—:—"}
        </span>
      </div>
      {playable.length > 1 && (
        <div className="mt-3 flex flex-wrap gap-2 border-t border-line pt-3">
          {playable.map((item, index) => (
            <button key={item.url} type="button" aria-pressed={index === selected} onClick={() => setSelected(index)}
              className={cn("rounded-full border px-3 py-1 text-xs transition", index === selected ? "border-[var(--accent-ink)] text-ink" : "border-line text-ink-muted hover:text-ink")}>
              {item.title}
            </button>
          ))}
        </div>
      )}
    </div>
  );
}
