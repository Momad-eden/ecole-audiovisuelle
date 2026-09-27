"use client";

import { useSyncExternalStore } from "react";
import { Moon, Sun } from "lucide-react";
import { cn } from "@/lib/utils";

export const THEME_STORAGE_KEY = "emsi-theme";

/** Appliqué avant l'affichage (layout) : pas de flash de l'autre thème au chargement. */
export const themeInitScript = `try{var t=localStorage.getItem("${THEME_STORAGE_KEY}");document.documentElement.dataset.theme=t==="light"?"light":"dark"}catch(e){}`;

type Theme = "dark" | "light";

function subscribe(callback: () => void) {
  const observer = new MutationObserver(callback);
  observer.observe(document.documentElement, { attributes: true, attributeFilter: ["data-theme"] });
  return () => observer.disconnect();
}

const readTheme = (): Theme => (document.documentElement.dataset.theme === "light" ? "light" : "dark");

/** Bascule entre le thème sombre « Plein feux » (par défaut) et le thème clair « Plein jour ». */
export function ThemeToggle({ className }: { className?: string }) {
  const theme = useSyncExternalStore(subscribe, readTheme, () => "dark" as Theme);
  const next: Theme = theme === "dark" ? "light" : "dark";

  function toggle() {
    document.documentElement.dataset.theme = next;
    try {
      localStorage.setItem(THEME_STORAGE_KEY, next);
    } catch {
      // Stockage indisponible (navigation privée) : le choix vaut pour la page en cours.
    }
  }

  return (
    <button
      type="button"
      onClick={toggle}
      className={cn("grid size-11 place-items-center rounded-full border border-line text-ink/80 transition hover:border-ink/40 hover:text-ink", className)}
      aria-label={next === "light" ? "Passer au thème clair" : "Passer au thème sombre"}
      title={next === "light" ? "Thème clair" : "Thème sombre"}
    >
      {theme === "dark" ? <Sun className="size-5" aria-hidden /> : <Moon className="size-5" aria-hidden />}
    </button>
  );
}
