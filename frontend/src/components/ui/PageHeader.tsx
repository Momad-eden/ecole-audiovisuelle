import type { ReactNode } from "react";
import { cn, frenchSpacing } from "@/lib/utils";

type Props = { eyebrow?: string | null; title: string; text?: string | null; accent?: string; children?: ReactNode; aside?: ReactNode; className?: string };

/** En-tête des pages intérieures : grand titre d'affiche éclairé par la couleur de la page. */
export function PageHeader({ eyebrow, title, text, accent, children, aside, className }: Props) {
  return (
    <section className={cn("relative isolate overflow-hidden pb-14 pt-36 sm:pt-44", className)} style={accent ? { ["--accent" as string]: accent } : undefined}>
      <div className="beam absolute inset-0 -z-10" aria-hidden />
      <div className={cn("mx-auto grid max-w-7xl gap-12 px-4 sm:px-6 lg:px-8", aside && "lg:grid-cols-[1.2fr_1fr] lg:items-center")}>
        <div>
          {eyebrow && (
            <p className="cartel mb-5 flex items-center gap-3 text-[var(--accent-ink)]">
              <span className="h-px w-10 bg-[var(--accent-ink)]" aria-hidden />
              {eyebrow}
            </p>
          )}
          <h1 className="display text-[clamp(2.6rem,7vw,6.2rem)] text-balance">{frenchSpacing(title)}</h1>
          {text && <p className="mt-6 max-w-2xl text-lg text-ink/80 sm:text-xl">{text}</p>}
          {children}
        </div>
        {aside}
      </div>
    </section>
  );
}
