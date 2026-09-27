import Link from "next/link";
import { ArrowRight } from "lucide-react";
import type { RoomSummary } from "@/lib/types";
import { MediaImage } from "@/components/ui/MediaImage";

/** « Porte » d'une salle : un seuil sombre traversé par la lumière de la salle. */
export function RoomDoor({ room }: { room: RoomSummary }) {
  return (
    <Link
      href={`/musee/${room.slug}`}
      className="group relative flex min-h-80 flex-col justify-end overflow-hidden rounded-3xl border border-line bg-night-2 p-6 transition hover:border-[var(--accent)]"
      style={{ ["--accent" as string]: room.accentColor }}
    >
      <MediaImage image={room.cover} sizes="(min-width: 1024px) 25vw, (min-width: 640px) 50vw, 100vw" className="opacity-40 transition duration-700 group-hover:scale-105 group-hover:opacity-60" />
      <div className="absolute inset-0 transition duration-700 group-hover:opacity-100" style={{ background: "radial-gradient(90% 70% at 50% -10%, color-mix(in oklab, var(--accent) 45%, transparent), transparent 65%)" }} aria-hidden />
      <div className="absolute inset-x-6 top-0 h-px bg-[var(--accent)] opacity-60" aria-hidden />
      <div className="relative">
        <p className="cartel" style={{ color: "var(--accent)" }}>{room.artworksCount !== undefined ? `${room.artworksCount} œuvre${room.artworksCount > 1 ? "s" : ""}` : "Salle"}</p>
        <h3 className="mt-2 font-display text-3xl">{room.name}</h3>
        {room.tagline && <p className="mt-2 text-sm text-ink/75">{room.tagline}</p>}
        <span className="mt-5 inline-flex items-center gap-2 text-sm font-medium text-[var(--accent)]">
          Entrer <ArrowRight className="size-4 transition group-hover:translate-x-1" aria-hidden />
        </span>
      </div>
    </Link>
  );
}
