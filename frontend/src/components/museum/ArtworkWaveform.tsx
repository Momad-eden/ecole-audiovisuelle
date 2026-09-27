"use client";

import { useAudio, type Track } from "@/components/audio/AudioProvider";
import { Waveform } from "@/components/audio/Waveform";

/** Forme d'onde de l'œuvre, synchronisée avec le lecteur global. */
export function ArtworkWaveform({ track }: { track: Track }) {
  const { track: current, currentTime, duration, seek, play } = useAudio();
  const isCurrent = current?.src === track.src;
  const progress = isCurrent && duration ? currentTime / duration : 0;

  return (
    <Waveform
      peaks={track.peaks}
      progress={progress}
      onSeek={(ratio) => (isCurrent ? seek(ratio) : play(track))}
      label={`Position dans « ${track.title} »`}
      className="mt-5 h-16"
    />
  );
}
