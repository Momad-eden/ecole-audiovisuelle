"use client";

import { LocaleLink } from "@/components/i18n/LocaleLink";
import { useEffect, useId, useRef, useState, type FocusEvent, type KeyboardEvent, type PointerEvent } from "react";
import { ArrowRight, ChevronDown } from "lucide-react";
import { MediaImage } from "@/components/ui/MediaImage";
import { accentVars } from "@/lib/contrast";
import type { Domains, MenuLink } from "@/lib/types";
import { cn } from "@/lib/utils";
import { MenuIcon, menuDomain } from "./menu-icons";

type Props = {
  link: MenuLink;
  /** Couleur du domaine de l'entrée (filet du panneau, point de la rubrique active). */
  color?: string;
  /** Vrai quand la page courante appartient à cette rubrique. */
  active?: boolean;
  /** Adresse du lien du sous-menu où l'on se trouve. */
  currentUrl?: string;
  domains?: Domains;
};

/**
 * Entrée de menu à sous-menu : s'ouvre au survol (souris), au clic, à Entrée/Espace ; Échap referme.
 * Le sous-menu est un grand panneau centré sous la barre : à gauche la rubrique (photo, phrase d'accroche,
 * lien « Découvrir »), à droite ses liens, chacun avec son icône teintée de la couleur de son domaine et sa description.
 */
export function NavDropdown({ link, color, active = false, currentUrl, domains }: Props) {
  const [open, setOpen] = useState(false);
  const rootRef = useRef<HTMLLIElement>(null);
  const buttonRef = useRef<HTMLButtonElement>(null);
  const listId = useId();
  const hoverOpened = useRef(false);
  const closeTimer = useRef<number | undefined>(undefined);
  const items = link.children ?? [];

  // Un seul menu ouvert à la fois, et un appui hors du menu le referme (les tablettes tactiles ne « quittent » jamais le bouton).
  useEffect(() => {
    if (!open) return;
    const others = (event: Event) => (event as CustomEvent<string>).detail !== listId && setOpen(false);
    const outside = (event: globalThis.PointerEvent) => !rootRef.current?.contains(event.target as Node) && setOpen(false);
    window.dispatchEvent(new CustomEvent("nav-dropdown-open", { detail: listId }));
    window.addEventListener("nav-dropdown-open", others);
    document.addEventListener("pointerdown", outside);
    return () => {
      window.removeEventListener("nav-dropdown-open", others);
      document.removeEventListener("pointerdown", outside);
    };
  }, [open, listId]);

  const links = () => Array.from(rootRef.current?.querySelectorAll<HTMLAnchorElement>("[data-nav-item]") ?? []);

  const onPointerEnter = (event: PointerEvent) => {
    window.clearTimeout(closeTimer.current);
    if (event.pointerType !== "mouse" || open) return;
    hoverOpened.current = true;
    setOpen(true);
  };
  // Le survol ne referme que ce qu'il a ouvert : après un clic, le menu reste ouvert jusqu'à Échap ou un appui ailleurs.
  const onPointerLeave = (event: PointerEvent) => {
    // Court délai : la souris traverse l'espace entre le bouton et le panneau sans le refermer.
    if (event.pointerType === "mouse" && hoverOpened.current) {
      closeTimer.current = window.setTimeout(() => {
        hoverOpened.current = false;
        setOpen(false);
      }, 200);
    }
  };
  const onClick = () => {
    if (open && hoverOpened.current) {
      hoverOpened.current = false; // ouvert par le survol : le clic l'épingle ouvert
      return;
    }
    setOpen(!open);
  };

  const onBlur = (event: FocusEvent) => {
    if (!rootRef.current?.contains(event.relatedTarget as Node | null)) setOpen(false);
  };

  const onKeyDown = (event: KeyboardEvent) => {
    if (event.key === "Escape" && open) {
      event.preventDefault();
      hoverOpened.current = false;
      setOpen(false);
      buttonRef.current?.focus();
      return;
    }
    if (event.key !== "ArrowDown" && event.key !== "ArrowUp") return;
    event.preventDefault();
    if (!open) {
      setOpen(true);
      // Les liens sont montés (masqués) en permanence : on peut viser le premier ou le dernier tout de suite.
      requestAnimationFrame(() => (event.key === "ArrowDown" ? links()[0] : links().at(-1))?.focus());
      return;
    }
    const list = links();
    const index = list.indexOf(document.activeElement as HTMLAnchorElement);
    const next = event.key === "ArrowDown" ? (index + 1) % list.length : (index <= 0 ? list.length : index) - 1;
    list[next]?.focus();
  };

  const wide = items.length > 3;

  return (
    <li ref={rootRef} onPointerEnter={onPointerEnter} onPointerLeave={onPointerLeave} onBlur={onBlur} onKeyDown={onKeyDown}>
      <button
        ref={buttonRef}
        type="button"
        aria-expanded={open}
        aria-controls={listId}
        onClick={onClick}
        className={cn("nav-pill", (active || open) && "nav-pill-active")}
      >
        {active && <span className="size-1.5 rounded-full bg-[var(--accent-ink)]" style={color ? { background: color } : undefined} aria-hidden />}
        {link.label}
        <ChevronDown className={cn("size-3.5 opacity-70 transition motion-reduce:transition-none", open && "rotate-180")} aria-hidden />
      </button>
      {/* Positionné par rapport à la barre (.header-bar) : centré sous l'en-tête, quelle que soit l'entrée. */}
      <div
        id={listId}
        className={cn(
          "absolute left-1/2 top-full z-10 w-[min(62rem,calc(100vw-1.5rem))] -translate-x-1/2 pt-3 transition duration-300 ease-out motion-reduce:transition-none",
          open ? "visible translate-y-0 opacity-100" : "pointer-events-none invisible -translate-y-2 opacity-0",
        )}
      >
        <div className={cn("nav-panel grid", wide ? "md:grid-cols-[19rem_1fr]" : "md:grid-cols-[17rem_1fr]")} style={accentVars(color)}>
          <LocaleLink href={link.url} data-nav-item onClick={() => setOpen(false)} className="group/feature relative m-2 flex min-h-56 flex-col justify-end overflow-hidden rounded-[1.1rem] p-6 max-md:hidden">
            {link.image ? (
              <span className="absolute inset-0 transition duration-700 group-hover/feature:scale-105" aria-hidden>
                <MediaImage image={{ ...link.image, alt: "" }} sizes="320px" />
              </span>
            ) : (
              <span className="absolute inset-0 bg-[radial-gradient(120%_90%_at_0%_0%,color-mix(in_oklab,var(--accent)_45%,transparent),transparent_65%)] bg-night-3" aria-hidden />
            )}
            <span className="absolute inset-0 bg-gradient-to-t from-[rgb(7_7_10/0.92)] via-[rgb(7_7_10/0.45)] to-transparent" aria-hidden />
            <span className="relative">
              <span className="display block text-2xl text-white">{link.label}</span>
              {link.description && <span className="mt-2 block text-sm leading-snug text-white/80">{link.description}</span>}
              <span className="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-white">
                <span className="h-px w-6 bg-[var(--accent)]" aria-hidden />
                <ArrowRight className="size-4 transition-transform group-hover/feature:translate-x-1" aria-hidden />
              </span>
            </span>
          </LocaleLink>
          <ul className={cn("grid content-center gap-1 p-2", wide && "sm:grid-cols-2")}>
            {items.map((item) => {
              const here = item.url === currentUrl;
              const itemColor = menuDomain(item.url) === "studio" ? domains?.studio?.color : undefined;
              return (
                <li key={item.url + item.label} style={accentVars(itemColor)}>
                  <LocaleLink
                    href={item.url}
                    data-nav-item
                    aria-current={here ? "page" : undefined}
                    onClick={() => setOpen(false)}
                    className={cn("group flex items-start gap-3 rounded-2xl p-3 transition hover:bg-ink/[0.06] focus-visible:bg-ink/[0.06]", here && "bg-ink/[0.06]")}
                  >
                    <span className="nav-icon">
                      <MenuIcon url={item.url} className="size-[1.15rem]" />
                    </span>
                    <span className="min-w-0 pt-0.5">
                      <span className="flex items-center gap-1.5 text-sm font-semibold text-ink">
                        {item.label}
                        {here && <span className="size-1.5 rounded-full bg-[var(--accent-ink)]" aria-hidden />}
                      </span>
                      {item.description && <span className="mt-0.5 block text-xs leading-snug text-ink-muted">{item.description}</span>}
                    </span>
                  </LocaleLink>
                </li>
              );
            })}
          </ul>
        </div>
      </div>
    </li>
  );
}
