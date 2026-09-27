"use client";

import Link from "next/link";
import { useEffect, useRef, useState } from "react";
import { ArrowRight, Menu, X } from "lucide-react";
import type { MenuLink, RoomSummary } from "@/lib/types";
import { ThemeToggle } from "./ThemeToggle";

export function MobileMenu({
    links,
    universes,
}: {
    links: MenuLink[];
    universes: RoomSummary[];
}) {
    const [open, setOpen] = useState(false);
    const dialogRef = useRef<HTMLDialogElement>(null);
    const navLinks = links.filter((link) => !link.isButton);
    const cta = links.find((link) => link.isButton);

    useEffect(() => {
        const dialog = dialogRef.current;
        if (!dialog) return;
        if (open && !dialog.open) dialog.showModal();
        if (!open && dialog.open) dialog.close();
    }, [open]);

    const close = () => setOpen(false);

    return (
        <>
            <button
                type="button"
                onClick={() => setOpen(true)}
                className="grid size-11 place-items-center rounded-full border border-line bg-night/60 lg:hidden"
                aria-label="Ouvrir le menu"
            >
                <Menu className="size-5" aria-hidden />
            </button>
            <dialog
                ref={dialogRef}
                onClose={close}
                className="m-0 h-dvh max-h-none w-full max-w-none bg-night p-0 text-ink backdrop:bg-night/80"
                aria-label="Menu"
            >
                <div className="beam flex min-h-full flex-col px-5 py-4">
                    <div className="flex items-center justify-between">
                        <span className="display text-2xl">EMSI</span>
                        <span className="flex items-center gap-2">
                            <ThemeToggle />
                            <button
                                type="button"
                                onClick={close}
                                className="grid size-11 place-items-center rounded-full border border-line"
                                aria-label="Fermer le menu"
                            >
                                <X className="size-5" aria-hidden />
                            </button>
                        </span>
                    </div>

                    <nav aria-label="Navigation principale" className="mt-8">
                        <ul>
                            {navLinks.map((link, index) => (
                                <li
                                    key={link.url}
                                    className="border-b border-line"
                                >
                                    <Link
                                        href={link.url}
                                        onClick={close}
                                        className="flex items-baseline justify-between py-4"
                                    >
                                        <span className="display text-3xl">
                                            {link.label}
                                        </span>
                                        <span className="cartel tabular-nums">
                                            {String(index + 1).padStart(2, "0")}
                                        </span>
                                    </Link>
                                </li>
                            ))}
                        </ul>
                    </nav>

                    {universes.length > 0 && (
                        <div className="mt-8">
                            <p className="cartel">Les univers</p>
                            <ul className="mt-3 flex flex-wrap gap-2">
                                {universes.map((universe) => (
                                    <li key={universe.id}>
                                        <Link
                                            href={`/univers/${universe.slug}`}
                                            onClick={close}
                                            className="inline-flex min-h-11 items-center gap-2 rounded-full border border-line px-4 text-sm"
                                            style={{
                                                ["--accent" as string]:
                                                    universe.accentColor,
                                            }}
                                        >
                                            <span
                                                className="size-2 rounded-full bg-[var(--accent-ink)] shadow-[0_0_10px_var(--accent)]"
                                                aria-hidden
                                            />
                                            {universe.name}
                                        </Link>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    )}

                    {cta && (
                        <Link
                            href={cta.url}
                            onClick={close}
                            className="mt-auto flex min-h-14 items-center justify-center gap-2 rounded-full bg-brand font-semibold text-on-accent"
                        >
                            {cta.label}{" "}
                            <ArrowRight className="size-4" aria-hidden />
                        </Link>
                    )}
                </div>
            </dialog>
        </>
    );
}
