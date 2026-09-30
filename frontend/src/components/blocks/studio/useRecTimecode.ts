"use client";

import { useEffect, type RefObject } from "react";
import { useReducedMotion } from "@/components/motion/useReducedMotion";

/** Timecode REC à 25 images/s, écrit directement dans le DOM (aucun rendu React), arrêté hors écran. */
export function useRecTimecode(sectionRef: RefObject<HTMLElement | null>, timecodeRef: RefObject<HTMLElement | null>) {
  const reducedMotion = useReducedMotion();

  useEffect(() => {
    const section = sectionRef.current;
    if (reducedMotion || !section) return;
    const start = performance.now();
    const pad = (n: number) => String(n).padStart(2, "0");
    let timer = 0;
    const tick = () => {
      const total = Math.floor(((performance.now() - start) / 1000) * 25);
      const f = total % 25, s = Math.floor(total / 25) % 60, m = Math.floor(total / 1500) % 60;
      if (timecodeRef.current) timecodeRef.current.textContent = `00:${pad(m)}:${pad(s)}:${pad(f)}`;
    };
    const observer = new IntersectionObserver(([entry]) => {
      window.clearInterval(timer);
      if (entry.isIntersecting) timer = window.setInterval(tick, 40);
    });
    observer.observe(section);
    return () => {
      observer.disconnect();
      window.clearInterval(timer);
    };
  }, [reducedMotion, sectionRef, timecodeRef]);
}
