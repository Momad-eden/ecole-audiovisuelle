"use client";

import { useEffect, useSyncExternalStore } from "react";

/**
 * Forme d'onde calculée dans le navigateur quand le serveur n'a pas pu la produire
 * (MP3 sans ffmpeg sur le serveur). Calculée une seule fois par fichier, au premier
 * lancement de la lecture : le fichier est alors déjà en cours de téléchargement.
 */
type Entry = number[] | "loading" | "error";

const cache = new Map<string, Entry>();
const listeners = new Set<() => void>();
const BARS = 160;

function notify() {
  listeners.forEach((listener) => listener());
}

/** Même origine que la page (/storage passe par le site), sinon le navigateur refuse la lecture du fichier. */
function sameOrigin(src: string): string {
  try {
    const url = new URL(src, window.location.href);
    return url.pathname.startsWith("/storage/") ? url.pathname : url.href;
  } catch {
    return src;
  }
}

async function load(src: string) {
  if (cache.has(src)) return;
  cache.set(src, "loading");
  notify();
  try {
    const buffer = await (await fetch(sameOrigin(src))).arrayBuffer();
    const Context = window.AudioContext ?? (window as unknown as { webkitAudioContext: typeof AudioContext }).webkitAudioContext;
    const context = new Context();
    const audio = await context.decodeAudioData(buffer);
    void context.close();
    const data = audio.getChannelData(0);
    const size = Math.floor(data.length / BARS) || 1;
    const peaks: number[] = [];
    for (let bar = 0; bar < BARS; bar++) {
      let max = 0;
      for (let i = bar * size; i < Math.min(data.length, (bar + 1) * size); i += 16) max = Math.max(max, Math.abs(data[i]));
      peaks.push(max);
    }
    const top = Math.max(...peaks, 0.001);
    cache.set(src, peaks.map((p) => Math.round((p / top) * 100) / 100));
  } catch {
    cache.set(src, "error");
  }
  notify();
}

function subscribe(listener: () => void) {
  listeners.add(listener);
  return () => listeners.delete(listener);
}

/** Pics fournis par le serveur, sinon calculés dans le navigateur dès que `enabled` est vrai. */
export function usePeaks(src: string | undefined, serverPeaks: number[] | null | undefined, enabled: boolean): number[] | null {
  const entry = useSyncExternalStore(subscribe, () => (src ? cache.get(src) : undefined), () => undefined);

  useEffect(() => {
    if (enabled && src && !serverPeaks?.length) void load(src);
  }, [enabled, src, serverPeaks]);

  if (serverPeaks?.length) return serverPeaks;
  return Array.isArray(entry) ? entry : null;
}
