import Link from "next/link";
import { CalendarDays } from "lucide-react";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { MediaImage } from "@/components/ui/MediaImage";
import { Container } from "@/components/ui/Section";
import { formatDate, frenchSpacing } from "@/lib/utils";
import type { CampusProgramsData } from "./types";

/** Formations ouvertes à la candidature dans un campus, avec la prochaine rentrée et « Candidater » prérempli. */
export function CampusProgramsBlock({ data, id }: { data: CampusProgramsData; id: string }) {
  if (!data.campus) return null;
  const items = data.items ?? [];
  const title = data.title || `Les formations à ${data.campus.city ?? data.campus.name}`;
  const headingId = `${id}-titre`;

  return (
    <section aria-labelledby={headingId} className="py-20 sm:py-28">
      <Container>
        <header className="mb-12 max-w-3xl">
          <p className="cartel mb-4 flex items-center gap-3">
            <span className="h-px w-10 bg-[var(--accent-ink)]" aria-hidden />
            {data.campus.name}
          </p>
          <h2 id={headingId} className="display text-[clamp(2rem,4.6vw,3.8rem)] text-balance">{frenchSpacing(title)}</h2>
        </header>

        {items.length === 0 ? (
          <p className="max-w-2xl rounded-3xl border border-line bg-night-2 p-8 text-lg text-ink-muted">
            Aucune formation n&apos;est ouverte pour le moment dans ce campus.
          </p>
        ) : (
          <ul className="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            {items.map((program) => (
              <li key={program.slug}>
                <article className="group relative flex h-full flex-col overflow-hidden rounded-[2rem] border border-line bg-night-2 transition-colors hover:border-[var(--accent)]">
                  <div className="relative aspect-[16/10] overflow-hidden border-b border-line bg-night-3" style={{ background: "radial-gradient(80% 70% at 60% 30%, color-mix(in oklab, var(--accent) 22%, transparent), transparent 70%)" }}>
                    <MediaImage image={program.cover} sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw" className="motion-safe:transition motion-safe:duration-700 group-hover:scale-105" />
                  </div>
                  <div className="flex flex-1 flex-col p-7">
                    <h3 className="display text-2xl leading-tight">
                      <Link href={`/emsi/formations/${program.slug}`} className="hover:text-[var(--accent-ink)]">{frenchSpacing(program.title)}</Link>
                    </h3>
                    {program.summary && <p className="mt-3 text-ink-muted">{frenchSpacing(program.summary)}</p>}
                    {program.nextStart && (
                      <p className="mt-5 flex items-center gap-2 text-sm font-medium text-ink/90">
                        <CalendarDays className="size-4 shrink-0 text-[var(--accent-ink)]" aria-hidden />
                        <span>Rentrée le {formatDate(program.nextStart)}</span>
                      </p>
                    )}
                    <div className="mt-auto pt-7">
                      <ButtonLink href={program.applyUrl}>
                        Candidater<span className="sr-only"> — {program.title}</span>
                      </ButtonLink>
                    </div>
                  </div>
                </article>
              </li>
            ))}
          </ul>
        )}
      </Container>
    </section>
  );
}
