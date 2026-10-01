import type { Locale } from "@/lib/i18n/locales";
import type { ArtworkSummary, Image, NewsItem, Program, RoomSummary } from "@/lib/types";

/** Props communes des blocs : données saisies dans l'admin et langue de la page (textes fixes, dates, liens). */
export type BlockProps<D> = { data: D; locale: Locale };

export type ButtonData = { label: string; url: string; style?: "primary" | "secondary" };

export type Hotspot = { x: number; y: number; label: string };
export type HeroTrack = { title: string; credits?: string | null; url: string | null };
export type CinemaSlide = { eyebrow?: string | null; title: string; image: Image; link?: { label: string; url: string } | null };

export type HeroData = { eyebrow?: string; title: string; subtitle?: string; image?: Image | null; videoLoop?: string | null; filmUrl?: string | null; layout?: "film" | "masterpiece" | "projection" | "cinema" | "stage" | "studio" | "events" | "spotlight" | "editorial" | "poster" | "mosaic" | "compact" | "full" | "split"; words?: string[]; images?: Image[]; caption?: string; sound?: string | null; accent?: string | null; buttons?: ButtonData[]; highlight?: string | null; hotspots?: Hotspot[]; tracks?: HeroTrack[]; slides?: CinemaSlide[]; facts?: { value: string; label: string }[] };
export type MarqueeData = { words?: string[] };
export type VenueData = { eyebrow?: string; title: string; text?: string; image?: Image | null; facts?: { value: string; label: string }[]; buttons?: ButtonData[] };
export type EquipmentData = { title?: string; text?: string; groups?: { category: string; items?: string[]; image?: Image | null }[] };
export type TextData = { title?: string; body?: string };
export type TextImageData = TextData & { image?: Image | null; imagePosition?: "left" | "right" };
export type GalleryData = { title?: string; layout?: "grid" | "mosaic" | "carousel"; images?: { image: Image | null; caption?: string }[] };
export type VideoData = { title?: string; url: string; poster?: Image | null; caption?: string; transcript?: string };
export type AudioData = { title?: string; description?: string; tracks?: { title: string; credits?: string | null; url: string | null }[] };
export type StatsData = { title?: string; items?: { value: string; label: string; detail?: string }[] };
export type QuoteData = { text: string; author?: string; role?: string; photo?: Image | null };
export type CtaData = { title: string; text?: string; buttons?: ButtonData[] };
export type CardsData = { title?: string; items?: { icon?: string; title: string; text?: string; url?: string }[] };
export type TimelineData = { title?: string; layout?: "list" | "steps"; steps?: { period: string; title: string; tag?: string; text?: string }[] };
export type FaqData = { title?: string; items?: { question: string; answer: string }[] };
export type ProgramsData = { title?: string; audience?: string; items?: Program[] };
export type ArtworksData = { title?: string; items?: ArtworkSummary[] };
export type RoomsData = { eyebrow?: string; title?: string; text?: string; items?: RoomSummary[] };
export type NewsData = { title?: string; items?: NewsItem[] };
export type PartnersData = { title?: string; items?: { name: string; category: string; website: string | null; logo: Image | null }[] };
export type ProfessionalSpaceData = { title: string; text?: string; image?: Image | null; buttonLabel?: string };
export type ContactData = { title?: string; text?: string; settings?: { phone?: string | null; whatsapp?: string | null; email?: string | null; address?: string | null; openingHours?: string | null; mapUrl?: string | null } };
export type DomainPanel = { domain: string; color: string; eyebrow?: string | null; title: string; text?: string | null; image?: Image | null; url: string; label?: string | null };
export type DomainsData = { intro?: string | null; panels?: DomainPanel[] };
export type CampusProgram = { title: string; slug: string; summary?: string | null; cover: Image | null; nextStart: string | null; applyUrl: string };
export type CampusProgramsData = { title?: string | null; campus: { id: number; name: string; slug: string; city: string | null } | null; items?: CampusProgram[] };
export type DownloadFile = { title: string; description?: string | null; url: string; size: number; extension: string };
export type DownloadsData = { title?: string | null; files?: DownloadFile[] };
export type SupportFormData = { title?: string | null; text?: string | null };
export type ShowcaseItem = { image: Image; video?: string | null; caption?: string | null; url?: string | null };
export type ShowcaseData = { eyebrow?: string | null; title: string; text?: string | null; items?: ShowcaseItem[]; buttonLabel?: string | null; buttonUrl?: string | null };
export type StatementData = { eyebrow?: string | null; text: string; facts?: { value: string; label: string }[]; images?: Image[]; buttonLabel?: string | null; buttonUrl?: string | null };
