"use client";

import type { KeyboardEvent, MouseEvent } from "react";

type Props = {
  peaks?: number[] | null;
  progress: number;
  onSeek?: (ratio: number) => void;
  label: string;
  className?: string;
};

/**
 * Forme d'onde précalculée par le serveur ; à défaut, simple barre de progression.
 * Utilisable au clavier (flèches gauche/droite) comme un curseur.
 */
export function Waveform({ peaks, progress, onSeek, label, className }: Props) {
  const seekFromPointer = (event: MouseEvent<HTMLDivElement>) => {
    const rect = event.currentTarget.getBoundingClientRect();
    onSeek?.((event.clientX - rect.left) / rect.width);
  };
  const seekFromKeyboard = (event: KeyboardEvent<HTMLDivElement>) => {
    if (event.key === "ArrowRight") onSeek?.(progress + 0.05);
    if (event.key === "ArrowLeft") onSeek?.(progress - 0.05);
  };

  const bars = peaks && peaks.length > 0 ? peaks : null;

  return (
    <div
      role="slider"
      tabIndex={0}
      aria-label={label}
      aria-valuemin={0}
      aria-valuemax={100}
      aria-valuenow={Math.round(progress * 100)}
      onClick={seekFromPointer}
      onKeyDown={seekFromKeyboard}
      className={`relative h-10 w-full cursor-pointer ${className ?? ""}`}
    >
      {bars ? (
        <svg viewBox={`0 0 ${bars.length} 100`} preserveAspectRatio="none" className="h-full w-full" aria-hidden="true">
          {bars.map((value, index) => {
            const height = Math.max(4, Math.min(100, value * 100));
            const played = index / bars.length <= progress;
            return (
              <rect
                key={index}
                x={index + 0.15}
                y={(100 - height) / 2}
                width={0.7}
                height={height}
                rx={0.35}
                fill={played ? "var(--accent)" : "rgb(242 238 230 / 0.25)"}
              />
            );
          })}
        </svg>
      ) : (
        <div className="absolute inset-x-0 top-1/2 h-1 -translate-y-1/2 rounded-full bg-ink/20" aria-hidden="true">
          <div className="h-full rounded-full" style={{ width: `${progress * 100}%`, background: "var(--accent)" }} />
        </div>
      )}
    </div>
  );
}
