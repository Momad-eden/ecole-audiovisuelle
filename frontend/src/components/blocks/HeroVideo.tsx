"use client";

import { useSyncExternalStore } from "react";

const QUERY = "(prefers-reduced-motion: reduce)";

function subscribe(callback: () => void) {
  const media = window.matchMedia(QUERY);
  media.addEventListener("change", callback);
  return () => media.removeEventListener("change", callback);
}

/** Boucle vidéo muette d'arrière-plan, désactivée si l'internaute limite les animations. */
export function HeroVideo({ src, poster }: { src: string; poster?: string | null }) {
  const reducedMotion = useSyncExternalStore(subscribe, () => window.matchMedia(QUERY).matches, () => true);

  if (reducedMotion) return null;

  return (
    <video className="absolute inset-0 h-full w-full object-cover opacity-45" src={src} poster={poster ?? undefined} autoPlay muted loop playsInline aria-hidden preload="metadata" />
  );
}
