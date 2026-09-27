import Link from "next/link";
import { ArrowUpRight } from "lucide-react";
import { cn, isExternal } from "@/lib/utils";

type Props = { href: string; children: React.ReactNode; variant?: "primary" | "secondary"; className?: string };

export function ButtonLink({ href, children, variant = "primary", className }: Props) {
  const classes = cn(
    "inline-flex min-h-11 items-center gap-2 rounded-full px-6 py-3 text-sm font-semibold transition",
    variant === "primary"
      ? "bg-[var(--accent)] text-night shadow-[0_0_40px_-10px_var(--accent)] hover:brightness-110"
      : "border border-line text-ink hover:border-[var(--accent)] hover:text-[var(--accent)]",
    className,
  );

  if (isExternal(href)) {
    return (
      <a href={href} className={classes} target="_blank" rel="noopener noreferrer">
        {children}
        <ArrowUpRight className="size-4" aria-hidden />
        <span className="sr-only">(nouvel onglet)</span>
      </a>
    );
  }

  return (
    <Link href={href} className={classes}>
      {children}
    </Link>
  );
}
