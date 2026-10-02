"use client";

import { useEffect, useRef, useState } from "react";
import { useReducedMotion } from "./useReducedMotion";

/**
 * Chiffre clé qui défile jusqu'à sa valeur quand il apparaît à l'écran (« 2 », « +300 », « 98 % », « 2016 »).
 * Seule la partie numérique bouge ; une année part de quelques décennies plus tôt. Texte final dès le rendu
 * serveur (lecteurs d'écran, moteurs de recherche) et sans animation si l'internaute la limite.
 */
export function CountUp({ value, className }: { value: string; className?: string }) {
  const ref = useRef<HTMLSpanElement>(null);
  const reducedMotion = useReducedMotion();
  const match = value.match(/^(\D*)(\d+)(\D*)$/);
  const target = match ? Number(match[2]) : null;
  const [shown, setShown] = useState<string>(value);

  useEffect(() => {
    const element = ref.current;
    if (!element || target === null || reducedMotion || !match) return;
    const year = match[2].length === 4 && target >= 1900 && target <= 2100;
    const from = year ? target - 30 : 0;
    let frame = 0;
    const observer = new IntersectionObserver(([entry]) => {
      if (!entry.isIntersecting) return;
      observer.disconnect();
      const start = performance.now();
      const step = (now: number) => {
        const progress = Math.min(1, (now - start) / 1400);
        const eased = 1 - Math.pow(1 - progress, 3);
        setShown(`${match[1]}${Math.round(from + (target - from) * eased)}${match[3]}`);
        if (progress < 1) frame = requestAnimationFrame(step);
      };
      setShown(`${match[1]}${from}${match[3]}`);
      frame = requestAnimationFrame(step);
    }, { threshold: 0.6 });
    observer.observe(element);
    return () => { observer.disconnect(); cancelAnimationFrame(frame); };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [value, reducedMotion]);

  return (
    <span ref={ref} className={className}>
      <span aria-hidden className="tabular-nums">{shown}</span>
      <span className="sr-only">{value}</span>
    </span>
  );
}
