import { cn } from "@/lib/utils";
import { Emphasis } from "./Emphasis";

export function Container({ children, className }: { children: React.ReactNode; className?: string }) {
  return <div className={cn("mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8", className)}>{children}</div>;
}

export function Section({ children, className, id }: { children: React.ReactNode; className?: string; id?: string }) {
  return (
    <section id={id} className={cn("py-20 sm:py-28", className)}>
      <Container>{children}</Container>
    </section>
  );
}

export function SectionTitle({ eyebrow, title, text, className }: { eyebrow?: string | null; title?: string | null; text?: string | null; className?: string }) {
  if (!title && !eyebrow) return null;
  return (
    <header className={cn("mb-12 max-w-3xl", className)}>
      {eyebrow && (
        <p className="cartel mb-4 flex items-center gap-3 text-[var(--accent-ink)]">
          <span className="h-px w-10 bg-[var(--accent-ink)]" aria-hidden />
          {eyebrow}
        </p>
      )}
      {title && <h2 className="display text-[clamp(2.2rem,4.4vw,3.6rem)] text-balance"><Emphasis text={title} /></h2>}
      {text && <p className="mt-5 text-lg text-ink-muted">{text}</p>}
    </header>
  );
}
