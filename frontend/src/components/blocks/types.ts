import type { ArtworkSummary, Image, NewsItem, Program, RoomSummary } from "@/lib/types";

export type ButtonData = { label: string; url: string; style?: "primary" | "secondary" };

export type HeroData = { eyebrow?: string; title: string; subtitle?: string; image?: Image | null; videoLoop?: string | null; layout?: "masterpiece" | "stage" | "studio" | "events" | "spotlight" | "editorial" | "poster" | "mosaic" | "compact" | "full" | "split"; words?: string[]; images?: Image[]; caption?: string; sound?: string | null; accent?: string | null; buttons?: ButtonData[] };
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
