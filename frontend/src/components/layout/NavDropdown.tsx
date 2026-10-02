"use client";

import { LocaleLink } from "@/components/i18n/LocaleLink";
import { useEffect, useId, useRef, useState, type FocusEvent, type KeyboardEvent, type PointerEvent } from "react";
import { ChevronDown } from "lucide-react";
import type { MenuLink } from "@/lib/types";
import { cn } from "@/lib/utils";

/** Entrée de menu à sous-menu : s'ouvre au survol (souris), au clic, à Entrée/Espace ; Échap referme. */
export function NavDropdown({ link }: { link: MenuLink }) {
  const [open, setOpen] = useState(false);
  const rootRef = useRef<HTMLLIElement>(null);
  const buttonRef = useRef<HTMLButtonElement>(null);
  const listId = useId();
  const hoverOpened = useRef(false);
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
    if (event.pointerType !== "mouse" || open) return;
    hoverOpened.current = true;
    setOpen(true);
  };
  // Le survol ne referme que ce qu'il a ouvert : après un clic, le menu reste ouvert jusqu'à Échap ou un appui ailleurs.
  const onPointerLeave = (event: PointerEvent) => {
    if (event.pointerType === "mouse" && hoverOpened.current) {
      hoverOpened.current = false;
      setOpen(false);
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

  return (
    <li ref={rootRef} className="relative" onPointerEnter={onPointerEnter} onPointerLeave={onPointerLeave} onBlur={onBlur} onKeyDown={onKeyDown}>
      <button
        ref={buttonRef}
        type="button"
        aria-expanded={open}
        aria-controls={listId}
        onClick={onClick}
        className="inline-flex items-center gap-1 rounded-full px-4 py-2 text-sm text-ink/80 transition hover:bg-ink/5 hover:text-ink"
      >
        {link.label}
        <ChevronDown className={cn("size-3.5 transition motion-reduce:transition-none", open && "rotate-180")} aria-hidden />
      </button>
      <div id={listId} className={cn("absolute left-0 top-full min-w-56 pt-3 transition duration-200 motion-reduce:transition-none", open ? "visible opacity-100" : "invisible opacity-0")}>
        <ul className="rounded-2xl border border-line bg-night-2/95 p-2 shadow-2xl backdrop-blur-xl">
          {items.map((item) => (
            <li key={item.url}>
              <LocaleLink href={item.url} data-nav-item onClick={() => setOpen(false)} className="block rounded-xl px-4 py-2.5 text-sm text-ink/85 transition hover:bg-ink/5 hover:text-ink">
                {item.label}
              </LocaleLink>
            </li>
          ))}
        </ul>
      </div>
    </li>
  );
}
