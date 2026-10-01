"use client";

import { LocaleLink } from "@/components/i18n/LocaleLink";
import { useEffect, useRef, useState } from "react";
import { usePathname } from "next/navigation";
import { ArrowRight, Menu, X } from "lucide-react";
import { ChevronDown } from "lucide-react";
import { useT } from "@/components/i18n/LocaleProvider";
import { accentVars } from "@/lib/contrast";
import { domainSection } from "@/lib/domains";
import { delocalizedPath } from "@/lib/i18n/locales";
import type { Domains, MenuLink } from "@/lib/types";
import { cn } from "@/lib/utils";
import { menuColor } from "./MainNav";
import { MenuIcon, menuDomain } from "./menu-icons";
import { LanguageSwitcher } from "./LanguageSwitcher";
import { ThemeToggle } from "./ThemeToggle";

export function MobileMenu({ links, domains }: { links: MenuLink[]; domains?: Domains }) {
    const t = useT().header;
    const [open, setOpen] = useState(false);
    const [expanded, setExpanded] = useState<string | null>(null);
    const dialogRef = useRef<HTMLDialogElement>(null);
    const navLinks = links.filter((link) => !link.isButton);
    const cta = links.find((link) => link.isButton);
    const path = delocalizedPath(usePathname() || "/");
    const clean = path.length > 1 ? path.replace(/\/+$/, "") : path;
    const section = domainSection(navLinks, clean);

    useEffect(() => {
        const dialog = dialogRef.current;
        if (!dialog) return;
        if (open && !dialog.open) dialog.showModal();
        if (!open && dialog.open) dialog.close();
    }, [open]);

    const close = () => setOpen(false);
    const openMenu = () => {
        setExpanded(section?.parent.children?.length ? section.parent.url + section.parent.label : null);
        setOpen(true);
    };

    return (
        <>
            <button
                type="button"
                onClick={openMenu}
                className="grid size-11 place-items-center rounded-full border border-line bg-night/60 lg:hidden"
                aria-label={t.openMenu}
            >
                <Menu className="size-5" aria-hidden />
            </button>
            <dialog
                ref={dialogRef}
                onClose={close}
                className="m-0 h-dvh max-h-none w-full max-w-none bg-night p-0 text-ink backdrop:bg-night/80"
                aria-label={t.menu}
            >
                <div className="beam flex min-h-full flex-col px-5 py-4">
                    <div className="flex items-center justify-between">
                        <span className="display text-2xl">EMSI</span>
                        <span className="flex items-center gap-2">
                            <LanguageSwitcher />
                            <ThemeToggle />
                            <button
                                type="button"
                                onClick={close}
                                className="grid size-11 place-items-center rounded-full border border-line"
                                aria-label={t.closeMenu}
                            >
                                <X className="size-5" aria-hidden />
                            </button>
                        </span>
                    </div>

                    <nav aria-label={t.mainNav} className="mt-8">
                        <ul>
                            {navLinks.map((link, index) => {
                                const key = link.url + link.label;
                                const children = link.children ?? [];
                                const isOpen = expanded === key;
                                const active = section?.parent === link || clean === link.url;
                                const color = menuColor(link.url, domains);
                                const number = (
                                    <span className="cartel tabular-nums">
                                        {String(index + 1).padStart(2, "0")}
                                    </span>
                                );
                                return (
                                    <li key={key} className="border-b border-line" style={accentVars(color)}>
                                        {children.length > 0 ? (
                                            <>
                                                <button
                                                    type="button"
                                                    aria-expanded={isOpen}
                                                    aria-controls={`mobile-sub-${index}`}
                                                    onClick={() => setExpanded(isOpen ? null : key)}
                                                    className="flex w-full items-baseline justify-between py-4 text-left"
                                                >
                                                    <span className={cn("display text-3xl", active && "text-[var(--accent-ink)]")}>{link.label}</span>
                                                    <span className="flex items-center gap-3">
                                                        {number}
                                                        <ChevronDown className={"size-4 self-center transition motion-reduce:transition-none " + (isOpen ? "rotate-180" : "")} aria-hidden />
                                                    </span>
                                                </button>
                                                <ul id={`mobile-sub-${index}`} hidden={!isOpen} className="grid gap-1 pb-4">
                                                    {children.map((child) => {
                                                        const here = section?.current?.url === child.url && section.parent === link;
                                                        const studio = menuDomain(child.url) === "studio" ? domains?.studio?.color : undefined;
                                                        return (
                                                            <li key={child.url + child.label} style={accentVars(studio)}>
                                                                <LocaleLink
                                                                    href={child.url}
                                                                    onClick={close}
                                                                    aria-current={here ? "page" : undefined}
                                                                    className={cn("flex items-center gap-3 rounded-2xl p-2.5 transition active:bg-ink/[0.08]", here && "bg-ink/[0.06]")}
                                                                >
                                                                    <span className="nav-icon">
                                                                        <MenuIcon url={child.url} className="size-[1.15rem]" />
                                                                    </span>
                                                                    <span className="min-w-0">
                                                                        <span className="block font-semibold text-ink">{child.label}</span>
                                                                        {child.description && <span className="block text-sm leading-snug text-ink-muted">{child.description}</span>}
                                                                    </span>
                                                                </LocaleLink>
                                                            </li>
                                                        );
                                                    })}
                                                </ul>
                                            </>
                                        ) : (
                                            <LocaleLink href={link.url} onClick={close} aria-current={active ? "page" : undefined} className="flex items-baseline justify-between py-4">
                                                <span className={cn("display text-3xl", active && "text-[var(--accent-ink)]")}>{link.label}</span>
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
