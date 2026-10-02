"use client";

import { useAudio, type Track } from "@/components/audio/AudioProvider";
import { Waveform } from "@/components/audio/Waveform";
import { usePeaks } from "@/components/audio/peaks";
import { useT } from "@/components/i18n/LocaleProvider";

/** Forme d'onde de l'œuvre, synchronisée avec le lecteur global. */
export function ArtworkWaveform({ track }: { track: Track }) {
  const { track: current, currentTime, duration, seek, play } = useAudio();
  const t = useT().audio;
  const isCurrent = current?.src === track.src;
  const progress = isCurrent && duration ? currentTime / duration : 0;
  const peaks = usePeaks(track.src, track.peaks, isCurrent);

  return (
    <Waveform
      peaks={peaks}
      progress={progress}
      onSeek={(ratio) => (isCurrent ? seek(ratio) : play(track))}
      label={t.position(track.title)}
      className="mt-5 h-16"
    />
  );
}
