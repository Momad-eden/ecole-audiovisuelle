import type { Locale } from "./i18n/locales";
// Types des réponses de l'API publique Laravel (/api/v1/public).

export type Image = { url: string; alt: string };

export type MenuChild = { label: string; url: string; description?: string | null };

/** Entrée du menu ; `children` : sous-menu d'un niveau (vide pour les liens simples). */
export type MenuLink = { label: string; url: string; isButton: boolean; children?: MenuChild[] };

/** Les trois domaines de la Maison, plus le domaine général (accueil, contact…). */
export type DomainKey = "general" | "maison" | "emsi" | "studio";
export type Domains = Record<DomainKey, { label: string; color: string }>;

/** Signature animée d'un univers (voir UniverseVisual). */
export type UniverseVisualKind = "sound" | "image" | "design" | "stage" | "cinema";

export type Track = {
  id: number;
  name: string;
  slug: string;
  shortName: string;
  summary: string | null;
  skills: string[];
  outcomes: string[];
};

/** Un univers de l'école (Son, Image, Infographie & design, Scène, Cinéma…). */
export type RoomSummary = {
  id: number;
  name: string;
  slug: string;
  tagline: string | null;
  intro: string | null;
  accentColor: string;
  visual: UniverseVisualKind;
  isUpcoming: boolean;
  cover: Image | null;
  artworksCount?: number;
  artworks?: ArtworkSummary[];
  tracks?: Track[];
  programs?: Program[];
};

export type Site = {
  settings: {
    schoolName: string;
    description: string | null;
    logo: Image | null;
    phone: string | null;
    whatsapp: string | null;
    email: string | null;
    address: string | null;
    openingHours: string | null;
    mapUrl: string | null;
    seoTitle: string | null;
    seoDescription: string | null;
    social: Partial<Record<"facebook" | "instagram" | "youtube" | "tiktok" | "linkedin" | "twitter", string>>;
  };
  menus: { main: MenuLink[]; footer: MenuLink[]; legal: MenuLink[] };
  domains: Domains;
  rooms: RoomSummary[];
  places: Place[];
  hasSchoolPrograms: boolean;
};

export type Block = { id: string; type: string; data: Record<string, unknown> };

export type Page = {
  title: string;
  slug: string;
  type: string;
  domain: DomainKey;
  seo: { title?: string; description?: string } | null;
  blocks: Block[];
  updatedAt: string | null;
  /** Langue demandée, langue réelle du contenu (« fr » tant que la page n'est pas traduite) et adresses dans chaque langue. */
  locale?: Locale;
  contentLocale?: Locale;
  alternates?: Record<Locale, string>;
};

export type ArtworkSummary = {
  id: number;
  title: string;
  slug: string;
  year: number | null;
  kind: string | null;
  kindLabel: string | null;
  summary: string | null;
  cover: Image | null;
  isFeatured: boolean;
  hasAudio: boolean;
  hasVideo: boolean;
  room?: { name: string; slug: string; accentColor: string } | null;
  track?: { name: string; slug: string } | null;
  origin?: "school" | "studio";
  /** Productions du studio seulement : écoute directe depuis la liste. */
  audio?: { url: string; peaks: number[] | null; durationSeconds: number | null } | null;
};

export type Artwork = ArtworkSummary & {
  creationStory: string | null;
  transcript: string | null;
  equipment: string[];
  audio: { url: string; peaks: number[] | null; durationSeconds: number | null } | null;
  videoUrl: string | null;
  durationSeconds: number | null;
  gallery: Image[];
  cohort?: string | null;
  credits?: { name: string; role: string }[];
  exhibitions?: { title: string; slug: string }[];
  publishedAt: string | null;
};

export type Exhibition = {
  id: number;
  title: string;
  slug: string;
  subtitle: string | null;
  startsOn: string | null;
  endsOn: string | null;
  state: "upcoming" | "current" | "past";
  venue: string | null;
  curatorialText: string | null;
  cover: Image | null;
  artworks?: ArtworkSummary[];
};

export type Offering = {
  id: number;
  label: string | null;
  track?: { name: string; slug: string } | null;
  capacity: number | null;
  feeAmount: number;
  registrationFeeAmount: number;
  fundingMode: string;
  fundingLabel: string | null;
  campusIds: number[];
  isOpen: boolean;
  audience: "school" | "professional" | null;
};

export type Cohort = {
  id: number;
  name: string;
  startsOn: string | null;
  endsOn: string | null;
  status: string;
  statusLabel: string;
  applicationsOpenAt: string | null;
  applicationsCloseAt: string | null;
  acceptsApplications: boolean;
  offerings?: Offering[];
};

export type Program = {
  id: number;
  title: string;
  slug: string;
  audience: "school" | "professional";
  kind: string;
  kindLabel: string;
  levelLabel: string | null;
  durationLabel: string | null;
  summary: string | null;
  cover: Image | null;
  description?: string | null;
  skills?: string[];
  outcomes?: string[];
  prerequisites?: string[];
  equipment?: string[];
  seo?: { title?: string; description?: string } | null;
  acceptsApplications?: boolean;
  cohorts?: Cohort[];
};

export type NewsItem = {
  id: number;
  title: string;
  slug: string;
  excerpt: string | null;
  image: Image | null;
  publishedAt: string | null;
  content?: string;
};

export type Paginated<T> = {
  data: T[];
  meta: { current_page: number; last_page: number; total: number; per_page: number };
};

// ——— Impact Live : studio, événementiel, Espace Habib Faye, lieux ———

export type Activity = "school" | "studio" | "events" | "space";

export type Place = {
  id: number;
  name: string;
  slug: string;
  kind: "campus" | "studio" | "cultural_center";
  city: string | null;
  address: string | null;
  phone: string | null;
  whatsapp: string | null;
  email: string | null;
  mapUrl: string | null;
  openingHours: string | null;
  tagline: string | null;
  description: string | null;
  highlights: string[];
  image: Image | null;
  /** Bloc « Nos campus » seulement : page du campus (« Découvrir le campus »), absente si elle n'est pas publiée. */
  pageUrl?: string | null;
};

export type Service = {
  id: number;
  name: string;
  slug: string;
  activity: Activity;
  summary: string | null;
  description: string | null;
  priceFrom: number | null;
  priceUnit: string | null;
  priceLabel: string;
  icon: string | null;
  image: Image | null;
};

export type EquipmentItem = {
  id: number;
  name: string;
  slug: string;
  brand: string | null;
  usage: "rental" | "studio";
  summary: string | null;
  image: Image | null;
  priceFrom: number | null;
  priceLabel: string;
  isFeatured: boolean;
  category?: { name: string; slug: string };
  description?: string | null;
  specs?: { label: string; value: string }[];
  quantity?: number | null;
  gallery?: Image[];
};

export type EquipmentCategory = { id: number; name: string; slug: string; summary: string | null; itemsCount: number };

export type RentalPack = {
  id: number;
  name: string;
  slug: string;
  summary: string | null;
  capacity: string | null;
  contents: string[];
  priceFrom: number | null;
  priceLabel: string;
  image: Image | null;
};

export type AgendaEvent = {
  id: number;
  title: string;
  slug: string;
  activity: Activity;
  activityLabel: string;
  venue: string | null;
  city: string | null;
  startsAt: string | null;
  endsAt: string | null;
  summary: string | null;
  content: string | null;
  image: Image | null;
  ticketUrl: string | null;
  isReference: boolean;
};

export type BookingType = "studio_session" | "equipment_rental" | "event_service" | "space_rental";
