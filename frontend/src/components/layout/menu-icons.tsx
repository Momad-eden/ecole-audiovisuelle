import {
  ArrowUpRight,
  BadgeCheck,
  BookOpen,
  CalendarDays,
  Clapperboard,
  Compass,
  Handshake,
  HeartHandshake,
  House,
  LayoutGrid,
  Mail,
  MapPin,
  Megaphone,
  Mic,
  Newspaper,
  School,
  type LucideIcon,
} from "lucide-react";
import type { DomainKey } from "@/lib/types";

/** Icône d'un lien de sous-menu, d'après son adresse (repli : flèche). */
const ICONS: Record<string, LucideIcon> = {
  "/centre-culturel": House,
  "/centre-culturel/agenda": CalendarDays,
  "/centre-culturel/studio": Mic,
  "/centre-culturel/espaces": LayoutGrid,
  "/emsi": School,
  "/emsi/dakar": MapPin,
  "/emsi/saint-louis": MapPin,
  "/emsi/formations": BookOpen,
  "/emsi/professionnels": BadgeCheck,
  "/emsi/realisations": Clapperboard,
  "/mission": Compass,
  "/partenaires": Handshake,
  "/soutenir": HeartHandshake,
  "/actualites": Newspaper,
  "/presse": Megaphone,
  "/contact": Mail,
};

export function MenuIcon({ url, className }: { url: string; className?: string }) {
  const Icon = ICONS[url] ?? ArrowUpRight;
  return <Icon className={className} aria-hidden />;
}

/** Domaine d'une adresse du menu, pour sa couleur (le studio a la sienne au sein du Centre culturel). */
export function menuDomain(url: string): DomainKey {
  if (url.startsWith("/centre-culturel/studio")) return "studio";
  if (url === "/centre-culturel" || url.startsWith("/centre-culturel/")) return "maison";
  if (url === "/emsi" || url.startsWith("/emsi/")) return "emsi";
  return "general";
}
