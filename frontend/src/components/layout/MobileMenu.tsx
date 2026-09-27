"use client";

import Link from "next/link";
import { useEffect, useRef, useState } from "react";
import { Menu, X } from "lucide-react";
import type { MenuLink } from "@/lib/types";

export function MobileMenu({ links }: { links: MenuLink[] }) {
  const [open, setOpen] = useState(false);
  const dialogRef = useRef<HTMLDialogElement>(null);

  useEffect(() => {
    const dialog = dialogRef.current;
    if (!dialog) return;
    if (open && !dialog.open) dialog.showModal();
    if (!open && dialog.open) dialog.close();
  }, [open]);

  return (
    <>
      <button type="button" onClick={() => setOpen(true)} className="grid size-11 place-items-center rounded-full border border-line lg:hidden" aria-label="Ouvrir le menu">
        <Menu className="size-5" aria-hidden />
      </button>
      <dialog
        ref={dialogRef}
        onClose={() => setOpen(false)}
        className="m-0 h-dvh max-h-none w-full max-w-none bg-night p-0 text-ink backdrop:bg-night/80"
        aria-label="Menu"
      >
        <div className="beam flex h-full flex-col px-6 py-5">
          <div className="flex items-center justify-between">
            <span className="font-display text-2xl font-semibold">EMSI</span>
            <button type="button" onClick={() => setOpen(false)} className="grid size-11 place-items-center rounded-full border border-line" aria-label="Fermer le menu">
              <X className="size-5" aria-hidden />
            </button>
          </div>
          <nav aria-label="Navigation principale" className="mt-10">
            <ul className="space-y-2">
              {links.map((link) => (
                <li key={link.url}>
                  <Link
                    href={link.url}
                    onClick={() => setOpen(false)}
                    className={link.isButton ? "mt-6 inline-flex min-h-12 items-center rounded-full bg-amber px-6 font-semibold text-night" : "block py-2 font-display text-3xl"}
                  >
                    {link.label}
                  </Link>
                </li>
              ))}
            </ul>
          </nav>
        </div>
      </dialog>
    </>
  );
}
