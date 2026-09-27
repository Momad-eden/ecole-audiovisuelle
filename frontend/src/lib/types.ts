// Types des réponses de l'API publique Laravel (/api/v1/public).

export type Image = { url: string; alt: string };

export type MenuLink = { label: string; url: string; isButton: boolean };

export type RoomSummary = {
  id: number;
  name: string;
  slug: string;
  tagline: string | null;
  intro: string | null;
  accentColor: string;
  cover: Image | null;
  artworksCount?: number;
  artworks?: ArtworkSummary[];
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
  rooms: RoomSummary[];
  hasSchoolPrograms: boolean;
};

export type Block = { id: string; type: string; data: Record<string, unknown> };

export type Page = {
  title: string;
  slug: string;
  type: string;
  seo: { title?: string; description?: string } | null;
  blocks: Block[];
  updatedAt: string | null;
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
