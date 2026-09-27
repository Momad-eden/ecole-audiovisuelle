import Image from "next/image";
import type { Image as ImageData } from "@/lib/types";
import { cn } from "@/lib/utils";

type Props = { image: ImageData | null | undefined; sizes: string; className?: string; priority?: boolean; fallbackAlt?: string };

/** Image optimisée (AVIF/WebP, tailles adaptées) occupant son conteneur. */
export function MediaImage({ image, sizes, className, priority, fallbackAlt = "" }: Props) {
  if (!image?.url) return null;
  return (
    <Image
      src={image.url}
      alt={image.alt || fallbackAlt}
      fill
      sizes={sizes}
      priority={priority}
      className={cn("object-cover", className)}
    />
  );
}
