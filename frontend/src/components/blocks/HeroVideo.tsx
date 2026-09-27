"use client";

import { useReducedMotion } from "@/components/motion/useReducedMotion";

/** Boucle vidéo muette d'arrière-plan, désactivée si l'internaute limite les animations. */
export function HeroVideo({ src, poster, className = "opacity-45" }: { src: string; poster?: string | null; className?: string }) {
  const reducedMotion = useReducedMotion();

  if (reducedMotion) return null;

  return (
    <video className={`absolute inset-0 h-full w-full object-cover ${className}`} src={src} poster={poster ?? undefined} autoPlay muted loop playsInline aria-hidden preload="metadata" />
  );
}
