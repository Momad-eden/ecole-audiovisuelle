import type { ReactNode } from "react";
import { InView } from "@/components/motion/InView";
import { UniverseVisual } from "@/components/universe/UniverseVisual";
import type { UniverseVisualKind } from "@/lib/types";

/** Contenu pas encore publié : un panneau soigné qui propose une suite, plutôt qu'une phrase perdue. */
export function EmptyState({ title, text, visual = "stage", children }: { title: string; text?: string; visual?: UniverseVisualKind; children?: ReactNode }) {
  return (
    <div className="grid overflow-hidden rounded-[2rem] border border-line bg-night-2 md:grid-cols-[1.3fr_1fr]">
      <div className="p-8 sm:p-12">
        <p className="display text-2xl sm:text-3xl">{title}</p>
        {text && <p className="mt-4 max-w-xl text-ink-muted">{text}</p>}
        {children && <div className="mt-8 flex flex-wrap gap-3">{children}</div>}
      </div>
      <div className="relative hidden min-h-56 border-l border-line md:block" style={{ background: "radial-gradient(80% 70% at 60% 30%, color-mix(in oklab, var(--accent) 20%, transparent), transparent 70%)" }} aria-hidden>
        <InView className="absolute inset-6 opacity-80"><UniverseVisual kind={visual} /></InView>
      </div>
    </div>
  );
}
