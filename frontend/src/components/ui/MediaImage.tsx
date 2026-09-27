import Image from "next/image";
import type { Image as ImageData } from "@/lib/types";
import { cn } from "@/lib/utils";

type Props = { image: ImageData | null | undefined; sizes: string; className?: string; priority?: boolean; fallbackAlt?: string; fit?: "cover" | "contain" };

/** Image optimisée (AVIF/WebP, tailles adaptées) occupant son conteneur ; « contain » pour les logos (jamais recadrés). */
export function MediaImage({ image, sizes, className, priority, fallbackAlt = "", fit = "cover" }: Props) {
  if (!image?.url) return null;
  return (
    <Image
      src={image.url}
      alt={image.alt || fallbackAlt}
      fill
      sizes={sizes}
      priority={priority}
      className={cn(fit === "contain" ? "object-contain" : "object-cover", className)}
    />
  );
}
