"use client";

import { useEffect, useRef, type ReactNode } from "react";

/** Suspend les animations CSS de son contenu tant qu'il est hors de l'écran (économie de batterie). */
export function InView({ children, className }: { children: ReactNode; className?: string }) {
  const ref = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const element = ref.current;
    if (!element) return;
    const observer = new IntersectionObserver(([entry]) => {
      element.dataset.paused = entry.isIntersecting ? "false" : "true";
    });
    observer.observe(element);
    return () => observer.disconnect();
  }, []);

  return (
    <div ref={ref} className={className} data-paused="true">
      {children}
    </div>
  );
}
