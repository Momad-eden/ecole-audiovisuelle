"use client";

import { useEffect, useState, type ReactNode } from "react";
import { cn } from "@/lib/utils";

/** En-tête transparent au-dessus du héros, qui se couvre d'un fond sombre dès qu'on défile. */
export function HeaderShell({ children }: { children: ReactNode }) {
  const [scrolled, setScrolled] = useState(false);

  useEffect(() => {
    const update = () => setScrolled(window.scrollY > 24);
    update();
    window.addEventListener("scroll", update, { passive: true });
    return () => window.removeEventListener("scroll", update);
  }, []);

  return (
    <header className={cn("site-header fixed inset-x-0 top-0 z-50 transition-colors duration-500", scrolled ? "border-b border-line bg-night/85 backdrop-blur-md" : "border-b border-transparent")}>
      {children}
    </header>
  );
}
