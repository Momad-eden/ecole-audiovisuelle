"use client";

import Link from "next/link";
import { useId, useRef, useState, type FocusEvent, type KeyboardEvent, type PointerEvent } from "react";
import { ChevronDown } from "lucide-react";
import type { MenuLink } from "@/lib/types";
import { cn } from "@/lib/utils";

/** Entrée de menu à sous-menu : s'ouvre au survol (souris), au clic, à Entrée/Espace ; Échap referme. */
export function NavDropdown({ link }: { link: MenuLink }) {
  const [open, setOpen] = useState(false);
  const rootRef = useRef<HTMLLIElement>(null);
  const buttonRef = useRef<HTMLButtonElement>(null);
  const listId = useId();
  const items = [{ label: `Tout ${link.label}`, url: link.url }, ...(link.children ?? [])];

  const links = () => Array.from(rootRef.current?.querySelectorAll<HTMLAnchorElement>("[data-nav-item]") ?? []);

  const onPointerEnter = (event: PointerEvent) => event.pointerType === "mouse" && setOpen(true);
  const onPointerLeave = (event: PointerEvent) => event.pointerType === "mouse" && setOpen(false);

  const onBlur = (event: FocusEvent) => {
    if (!rootRef.current?.contains(event.relatedTarget as Node | null)) setOpen(false);
  };

  const onKeyDown = (event: KeyboardEvent) => {
    if (event.key === "Escape" && open) {
      event.preventDefault();
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
        onClick={() => setOpen((value) => !value)}
        className="inline-flex items-center gap-1 rounded-full px-4 py-2 text-sm text-ink/80 transition hover:bg-ink/5 hover:text-ink"
      >
        {link.label}
        <ChevronDown className={cn("size-3.5 transition motion-reduce:transition-none", open && "rotate-180")} aria-hidden />
      </button>
      <div id={listId} className={cn("absolute left-0 top-full min-w-56 pt-3 transition duration-200 motion-reduce:transition-none", open ? "visible opacity-100" : "invisible opacity-0")}>
        <ul className="rounded-2xl border border-line bg-night-2/95 p-2 shadow-2xl backdrop-blur-xl">
          {items.map((item) => (
            <li key={item.url}>
              <Link href={item.url} data-nav-item onClick={() => setOpen(false)} className="block rounded-xl px-4 py-2.5 text-sm text-ink/85 transition hover:bg-ink/5 hover:text-ink">
                {item.label}
              </Link>
            </li>
          ))}
        </ul>
      </div>
    </li>
  );
}
