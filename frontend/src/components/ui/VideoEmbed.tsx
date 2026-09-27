"use client";

import Image from "next/image";
import { useState } from "react";
import { Play } from "lucide-react";
import { videoEmbed } from "@/lib/utils";

/** La vidéo n'est chargée qu'au clic : aucun cookie ni lecture automatique avant l'action du visiteur. */
export function VideoEmbed({ url, title, poster }: { url: string; title: string; poster?: string | null }) {
  const [active, setActive] = useState(false);
  const embed = videoEmbed(url);
  if (!embed) return null;

  const cover = poster ?? embed.thumbnail;

  return (
    <div className="relative aspect-video overflow-hidden rounded-2xl border border-line bg-night-3">
      {active ? (
        <iframe
          src={embed.embedUrl}
          title={title}
          allow="autoplay; encrypted-media; picture-in-picture; fullscreen"
          allowFullScreen
          className="absolute inset-0 h-full w-full"
        />
      ) : (
        <button type="button" onClick={() => setActive(true)} className="group absolute inset-0 grid place-items-center" aria-label={`Lire la vidéo « ${title} »`}>
          {cover && <Image src={cover} alt="" fill sizes="(min-width: 1024px) 60vw, 100vw" className="object-cover opacity-70 transition group-hover:opacity-90" />}
          <span className="relative grid size-20 place-items-center rounded-full bg-[var(--accent-ink)] text-on-accent shadow-[0_0_60px_-5px_var(--accent)]">
            <Play className="size-8 translate-x-0.5" aria-hidden />
          </span>
        </button>
      )}
    </div>
  );
}
