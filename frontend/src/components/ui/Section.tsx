import { cn } from "@/lib/utils";

export function Container({ children, className }: { children: React.ReactNode; className?: string }) {
  return <div className={cn("mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8", className)}>{children}</div>;
}

export function Section({ children, className, id }: { children: React.ReactNode; className?: string; id?: string }) {
  return (
    <section id={id} className={cn("py-16 sm:py-24", className)}>
      <Container>{children}</Container>
    </section>
  );
}

export function SectionTitle({ eyebrow, title, text, className }: { eyebrow?: string | null; title?: string | null; text?: string | null; className?: string }) {
  if (!title && !eyebrow) return null;
  return (
    <header className={cn("mb-10 max-w-3xl", className)}>
      {eyebrow && <p className="cartel mb-3">{eyebrow}</p>}
      {title && <h2 className="font-display text-3xl leading-tight text-balance sm:text-4xl">{title}</h2>}
      {text && <p className="mt-4 text-lg text-ink-muted">{text}</p>}
    </header>
  );
}
