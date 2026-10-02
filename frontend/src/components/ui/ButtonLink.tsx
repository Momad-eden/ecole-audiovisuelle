"use client";

import { LocaleLink } from "@/components/i18n/LocaleLink";
import { useT } from "@/components/i18n/LocaleProvider";
import { ArrowRight, ArrowUpRight } from "lucide-react";
import { cn, isExternal } from "@/lib/utils";

type Props = { href: string; children: React.ReactNode; variant?: "primary" | "secondary"; size?: "md" | "lg"; className?: string };

/** Lien en forme de bouton ; composant client pour annoncer « nouvel onglet » dans la langue de la page. */
export function ButtonLink({ href, children, variant = "primary", size = "md", className }: Props) {
  const t = useT().common;
  const classes = cn(
    "group inline-flex items-center gap-3 rounded-full font-semibold transition duration-300",
    size === "lg" ? "min-h-14 px-7 text-base" : "min-h-11 px-6 text-sm",
    variant === "primary"
      ? "bg-[var(--accent-ink)] text-on-accent shadow-[0_0_50px_-12px_var(--accent)] hover:shadow-[0_0_70px_-8px_var(--accent)] hover:brightness-110"
      : "border border-ink/25 bg-night/30 text-ink hover:border-[var(--accent)] hover:text-[var(--accent-ink)]",
    className,
  );

  if (isExternal(href)) {
    return (
      <a href={href} className={classes} target="_blank" rel="noopener noreferrer">
        {children}
        <ArrowUpRight className="size-4" aria-hidden />
        <span className="sr-only">{t.newTab}</span>
      </a>
    );
  }

  return (
    <LocaleLink href={href} className={classes}>
      {children}
      <ArrowRight className="size-4 transition-transform duration-300 group-hover:translate-x-1" aria-hidden />
    </LocaleLink>
  );
}
