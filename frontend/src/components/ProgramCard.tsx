import Link from "next/link";
import { ArrowRight } from "lucide-react";
import type { Program } from "@/lib/types";
import { MediaImage } from "@/components/ui/MediaImage";

export function programHref(program: Pick<Program, "audience" | "slug">): string {
  return program.audience === "professional" ? `/emsi/professionnels/${program.slug}` : `/emsi/formations/${program.slug}`;
}

export function ProgramCard({ program }: { program: Program }) {
  return (
    <Link href={programHref(program)} className="group flex h-full flex-col overflow-hidden rounded-3xl border border-line bg-night-2 transition duration-500 hover:-translate-y-1 hover:border-brand">
      {program.cover && (
        <div className="relative aspect-[16/9]"><MediaImage image={program.cover} sizes="(min-width: 1024px) 33vw, 100vw" className="transition duration-700 group-hover:scale-105" /></div>
      )}
      <div className="flex flex-1 flex-col p-7">
        <p className="cartel">{[program.kindLabel, program.durationLabel].filter(Boolean).join(" · ")}</p>
        <h3 className="display mt-3 text-2xl leading-tight">{program.title}</h3>
        {program.summary && <p className="mt-3 line-clamp-3 text-ink-muted">{program.summary}</p>}
        <span className="mt-auto inline-flex items-center gap-2 pt-6 text-sm font-semibold text-brand">Voir la formation <ArrowRight className="size-4 transition group-hover:translate-x-1" aria-hidden /></span>
      </div>
    </Link>
  );
}
