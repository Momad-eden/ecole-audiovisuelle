"use client";

import { LocaleLink } from "@/components/i18n/LocaleLink";
import { useEffect, useRef, useState } from "react";
import { ArrowRight, Menu, X } from "lucide-react";
import { ChevronDown } from "lucide-react";
import type { MenuLink } from "@/lib/types";
import { ThemeToggle } from "./ThemeToggle";

export function MobileMenu({ links }: { links: MenuLink[] }) {
    const [open, setOpen] = useState(false);
    const [expanded, setExpanded] = useState<string | null>(null);
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
                            {navLinks.map((link, index) => {
                                const key = link.url + link.label;
                                const children = link.children ?? [];
                                const isOpen = expanded === key;
                                const number = (
                                    <span className="cartel tabular-nums">
                                        {String(index + 1).padStart(2, "0")}
                                    </span>
                                );
                                return (
                                    <li key={key} className="border-b border-line">
                                        {children.length > 0 ? (
                                            <>
                                                <button
                                                    type="button"
                                                    aria-expanded={isOpen}
                                                    aria-controls={`mobile-sub-${index}`}
                                                    onClick={() => setExpanded(isOpen ? null : key)}
                                                    className="flex w-full items-baseline justify-between py-4 text-left"
                                                >
                                                    <span className="display text-3xl">{link.label}</span>
                                                    <span className="flex items-center gap-3">
                                                        {number}
                                                        <ChevronDown className={"size-4 self-center transition motion-reduce:transition-none " + (isOpen ? "rotate-180" : "")} aria-hidden />
                                                    </span>
                                                </button>
                                                <ul id={`mobile-sub-${index}`} hidden={!isOpen} className="pb-3">
                                                    {children.map((child) => (
                                                        <li key={child.url}>
                                                            <LocaleLink href={child.url} onClick={close} className="flex min-h-11 items-center pl-3 text-lg text-ink/85">
                                                                {child.label}
                                                            </LocaleLink>
                                                        </li>
                                                    ))}
                                                </ul>
                                            </>
                                        ) : (
                                            <LocaleLink href={link.url} onClick={close} className="flex items-baseline justify-between py-4">
                                                <span className="display text-3xl">{link.label}</span>
                                                {number}
                                            </LocaleLink>
                                        )}
                                    </li>
                                );
                            })}
                        </ul>
                    </nav>

                    {cta && (
                        <LocaleLink
                            href={cta.url}
                            onClick={close}
                            className="mt-auto flex min-h-14 items-center justify-center gap-2 rounded-full bg-brand font-semibold text-on-accent"
                        >
                            {cta.label}{" "}
                            <ArrowRight className="size-4" aria-hidden />
                        </LocaleLink>
                    )}
                </div>
            </dialog>
        </>
    );
}
