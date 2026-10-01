"use client";

import { useEffect, useState, type ReactNode } from "react";
import { cn } from "@/lib/utils";

/**
 * En-tête transparent au-dessus du héros. Dès qu'on défile, sa barre devient une capsule flottante (voir
 * .header-bar) ; elle se cache quand on descend et revient dès qu'on remonte. Jamais cachée quand un menu est
 * ouvert ou que le clavier y est. `html[data-header-hidden]` laisse la rangée de domaine suivre le mouvement.
 */
export function HeaderShell({ children }: { children: ReactNode }) {
  const [scrolled, setScrolled] = useState(false);
  const [hidden, setHidden] = useState(false);

  useEffect(() => {
    let last = window.scrollY;
    const update = () => {
      const y = window.scrollY;
      setScrolled(y > 24);
      const header = document.querySelector(".site-header");
      const busy = !!header?.querySelector("[aria-expanded='true']") || !!header?.contains(document.activeElement);
      if (y < 160 || busy || y < last - 6) setHidden(false);
      else if (y > last + 6) setHidden(true);
      if (Math.abs(y - last) > 6) last = y;
    };
    update();
    window.addEventListener("scroll", update, { passive: true });
    return () => window.removeEventListener("scroll", update);
  }, []);

  useEffect(() => {
    const root = document.documentElement;
    if (hidden) root.dataset.headerHidden = "";
    else delete root.dataset.headerHidden;
    return () => { delete root.dataset.headerHidden; };
  }, [hidden]);

  return (
    <header
      data-scrolled={scrolled ? "" : undefined}
      className={cn("site-header fixed inset-x-0 top-0 z-50 transition-transform duration-500 ease-out motion-reduce:transition-none", hidden && "-translate-y-[120%]")}
      onFocus={() => setHidden(false)}
    >
      {children}
    </header>
  );
}
