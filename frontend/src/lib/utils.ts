import { clsx, type ClassValue } from "clsx";

export function cn(...inputs: ClassValue[]) {
  return clsx(inputs);
}

/** 1250000 → « 1 250 000 FCFA » */
export function fcfa(amount: number): string {
  return `${new Intl.NumberFormat("fr-FR", { maximumFractionDigits: 0 }).format(amount).replace(/ /g, " ")} FCFA`;
}

export function formatDate(iso: string | null | undefined, options: Intl.DateTimeFormatOptions = { day: "numeric", month: "long", year: "numeric" }): string {
  if (!iso) return "";
  return new Intl.DateTimeFormat("fr-FR", { timeZone: "Africa/Dakar", ...options }).format(new Date(iso));
}

export function formatDuration(seconds: number | null | undefined): string {
  if (!seconds) return "";
  const m = Math.floor(seconds / 60);
  const s = Math.round(seconds % 60);
  return `${m}:${String(s).padStart(2, "0")}`;
}

export const siteUrl = process.env.NEXT_PUBLIC_SITE_URL ?? "http://localhost:3000";

export function isExternal(url: string): boolean {
  return /^https?:\/\//.test(url);
}

/** Extrait l'identifiant d'une vidéo YouTube ou Vimeo pour un lecteur intégré sans cookies. */
export function videoEmbed(url: string): { provider: "youtube" | "vimeo"; id: string; embedUrl: string; thumbnail: string | null } | null {
  const yt = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([\w-]{11})/);
  if (yt) {
    return { provider: "youtube", id: yt[1], embedUrl: `https://www.youtube-nocookie.com/embed/${yt[1]}?autoplay=1&rel=0`, thumbnail: `https://i.ytimg.com/vi/${yt[1]}/hqdefault.jpg` };
  }
  const vimeo = url.match(/vimeo\.com\/(?:video\/)?(\d+)/);
  if (vimeo) {
    return { provider: "vimeo", id: vimeo[1], embedUrl: `https://player.vimeo.com/video/${vimeo[1]}?autoplay=1&dnt=1`, thumbnail: null };
  }
  return null;
}

/** Typographie française : espace insécable avant « : ; ! ? » et à l'intérieur des guillemets, pour éviter les retours à la ligne orphelins. */
export function frenchSpacing<T extends string | null | undefined>(text: T): T {
  if (!text) return text;
  return text.replace(/ ([:;!?»])/g, " $1").replace(/« /g, "« ") as T;
}
