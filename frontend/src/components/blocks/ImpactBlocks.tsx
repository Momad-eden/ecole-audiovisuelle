import Link from "next/link";
import { ArrowRight, Clock, Disc3, Mail, MapPin, Phone } from "lucide-react";
import { PlayButton } from "@/components/audio/PlayButton";
import { ArtworkWaveform } from "@/components/museum/ArtworkWaveform";
import { InView } from "@/components/motion/InView";
import { Reveal } from "@/components/motion/Reveal";
import { LazyBookingForm } from "@/components/forms/LazyBookingForm";
import { AddToQuote } from "@/components/quote/AddToQuote";
import { MediaImage } from "@/components/ui/MediaImage";
import { Section, SectionTitle } from "@/components/ui/Section";
import { UniverseVisual } from "@/components/universe/UniverseVisual";
import type { Activity, AgendaEvent, ArtworkSummary, BookingType, EquipmentItem, Image, Place, RentalPack, Service, UniverseVisualKind } from "@/lib/types";
import { cn } from "@/lib/utils";

/** Lumière de chaque activité de l'écosystème. */
export const ACTIVITY_ACCENT: Record<Activity, string> = {
  school: "var(--color-brand)",
  studio: "var(--color-rec)",
  events: "var(--color-gold)",
  space: "var(--color-violet)",
};
const ACTIVITY_VISUAL: Record<Activity, UniverseVisualKind> = { school: "image", studio: "sound", events: "stage", space: "design" };

const accent = (activity: Activity) => ({ ["--accent" as string]: ACTIVITY_ACCENT[activity] });

// ——— Écosystème ———

type EcosystemData = { eyebrow?: string; title: string; text?: string; items?: { name: string; activity: Activity; text?: string; url?: string; image?: Image | null }[] };

export function EcosystemBlock({ data }: { data: EcosystemData }) {
  const items = data.items ?? [];
  return (
    <Section>
      <SectionTitle eyebrow={data.eyebrow} title={data.title} text={data.text} />
      <ul className="grid gap-5 md:grid-cols-2">
        {items.map((item, index) => {
          const body = (
            <>
              <div className="relative aspect-[16/9] overflow-hidden border-b border-line" style={{ background: "radial-gradient(80% 70% at 60% 35%, color-mix(in oklab, var(--accent) 24%, transparent), transparent 70%)" }}>
                {item.image ? <MediaImage image={item.image} sizes="(min-width: 768px) 50vw, 100vw" className="transition duration-700 group-hover:scale-105" /> : (
                  <InView className="absolute inset-4 transition duration-700 group-hover:scale-105"><UniverseVisual kind={ACTIVITY_VISUAL[item.activity] ?? "sound"} /></InView>
                )}
              </div>
              <div className="p-7">
                <p className="cartel tabular-nums text-[var(--accent-ink)]">{String(index + 1).padStart(2, "0")}</p>
                <h3 className="display mt-3 text-[clamp(1.6rem,3vw,2.4rem)]">{item.name}</h3>
                {item.text && <p className="mt-3 text-ink-muted">{item.text}</p>}
                {item.url && <span className="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-[var(--accent-ink)]">Découvrir <ArrowRight className="size-4 transition-transform group-hover:translate-x-1" aria-hidden /></span>}
              </div>
            </>
          );
          return (
            <Reveal as="li" key={item.name} delay={(index % 2) * 120} className="h-full">
              {item.url ? (
                <Link href={item.url} className="group block h-full overflow-hidden rounded-[2rem] border border-line bg-night-2 transition duration-500 hover:-translate-y-1 hover:border-[var(--accent)]" style={accent(item.activity)}>{body}</Link>
              ) : (
                <div className="h-full overflow-hidden rounded-[2rem] border border-line bg-night-2" style={accent(item.activity)}>{body}</div>
              )}
            </Reveal>
          );
        })}
      </ul>
    </Section>
  );
}

// ——— Services et tarifs ———

export function ServicesBlock({ data }: { data: { title?: string; text?: string; activity?: Activity; items?: Service[] } }) {
  const items = data.items ?? [];
  if (items.length === 0) return null;
  const activity = data.activity ?? items[0].activity;
  return (
    <Section>
      <div style={accent(activity)}>
        <SectionTitle eyebrow="Services et tarifs" title={data.title} text={data.text} />
        <ul className="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
          {items.map((service, index) => (
            <Reveal as="li" key={service.id} delay={(index % 3) * 100} className="flex h-full flex-col rounded-3xl border border-line bg-night-2 p-7">
              {service.image && <div className="relative -mx-7 -mt-7 mb-6 aspect-[16/9] overflow-hidden rounded-t-3xl"><MediaImage image={service.image} sizes="(min-width: 1024px) 33vw, 100vw" /></div>}
              <p className="cartel tabular-nums text-[var(--accent-ink)]">{String(index + 1).padStart(2, "0")}</p>
              <h3 className="display mt-3 text-2xl">{service.name}</h3>
              {service.summary && <p className="mt-3 text-ink-muted">{service.summary}</p>}
              <div className="mt-auto flex flex-wrap items-center justify-between gap-3 pt-6">
                <span className="rounded-full border border-line px-3 py-1.5 font-mono text-xs">{service.priceLabel}</span>
                {service.activity === "events" && <AddToQuote item={{ kind: "service", id: service.id, name: service.name }} compact />}
              </div>
            </Reveal>
          ))}
        </ul>
      </div>
    </Section>
  );
}

// ——— Matériel ———

export function EquipmentCard({ item }: { item: EquipmentItem }) {
  return (
    <article className="group flex h-full flex-col overflow-hidden rounded-3xl border border-line bg-night-2 transition duration-500 hover:border-[var(--accent)]">
      <Link href={`/events/materiel/${item.slug}`} className="block">
        <div className="relative aspect-[4/3] overflow-hidden border-b border-line bg-night-3">
          {item.image ? <MediaImage image={item.image} sizes="(min-width: 1024px) 25vw, (min-width: 640px) 50vw, 100vw" className="transition duration-700 group-hover:scale-105" /> : (
            <div className="absolute inset-0 grid place-items-center p-6 text-center" aria-hidden><span className="display text-3xl text-ink/15">{item.brand ?? item.name}</span></div>
          )}
          {item.category && <span className="cartel absolute left-4 top-4 rounded-full bg-night/80 px-3 py-1">{item.category.name}</span>}
        </div>
        <div className="p-6 pb-0">
          {item.brand && <p className="cartel">{item.brand}</p>}
          <h3 className="display mt-1 text-xl leading-tight transition group-hover:text-[var(--accent-ink)]">{item.name}</h3>
          <p className="mt-2 font-mono text-xs text-ink-muted">{item.priceLabel}</p>
        </div>
      </Link>
      <div className="mt-auto p-6">
        <AddToQuote item={{ kind: "equipment", id: item.id, name: item.name }} compact />
      </div>
    </article>
  );
}

export function EquipmentListBlock({ data }: { data: { title?: string; text?: string; usage?: "rental" | "studio"; items?: EquipmentItem[] } }) {
  const items = data.items ?? [];
  if (items.length === 0) return null;

  // Matériel du studio : une fiche technique par catégorie, sans location.
  if (data.usage === "studio") {
    const groups = Object.entries(items.reduce<Record<string, EquipmentItem[]>>((acc, item) => {
      (acc[item.category?.name ?? "Équipement"] ??= []).push(item);
      return acc;
    }, {}));
    return (
      <Section>
        <SectionTitle eyebrow="Fiche technique" title={data.title} text={data.text} />
        <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
          {groups.map(([category, list], index) => (
            <Reveal key={category} delay={(index % 3) * 100} className="rounded-3xl border border-line bg-night-2 p-7">
              <p className="cartel flex justify-between"><span>{category}</span><span className="text-[var(--accent-ink)]">{`S${index + 1}`}</span></p>
              <ul className="mt-5 space-y-3">
                {list.map((item) => (
                  <li key={item.id} className="flex items-baseline gap-3">
                    <span className="size-1.5 shrink-0 translate-y-[-2px] rounded-full bg-[var(--accent-ink)]" aria-hidden />
                    <span><span className="font-medium">{item.name}</span>{item.brand && <span className="text-ink-muted"> · {item.brand}</span>}</span>
                  </li>
                ))}
              </ul>
            </Reveal>
          ))}
        </div>
      </Section>
    );
  }

  return (
    <Section>
      <div style={accent("events")}>
        <div className="flex flex-wrap items-end justify-between gap-6">
          <SectionTitle eyebrow="Location" title={data.title} text={data.text} />
          <Link href="/events/materiel" className="group mb-12 inline-flex items-center gap-2 text-sm font-semibold text-[var(--accent-ink)]">Tout le matériel <ArrowRight className="size-4 transition-transform group-hover:translate-x-1" aria-hidden /></Link>
        </div>
        <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
          {items.map((item) => <EquipmentCard key={item.id} item={item} />)}
        </div>
      </div>
    </Section>
  );
}

// ——— Packs ———

export function PacksBlock({ data }: { data: { title?: string; text?: string; items?: RentalPack[] } }) {
  const items = data.items ?? [];
  if (items.length === 0) return null;
  return (
    <Section>
      <div style={accent("events")}>
        <SectionTitle eyebrow="Packs" title={data.title} text={data.text} />
        <ul className="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
          {items.map((pack, index) => (
            <Reveal as="li" key={pack.id} delay={(index % 3) * 100} className="flex h-full flex-col overflow-hidden rounded-[2rem] border border-line bg-night-2">
              <div className="relative aspect-[16/9] border-b border-line" style={{ background: "radial-gradient(80% 70% at 60% 30%, color-mix(in oklab, var(--accent) 25%, transparent), transparent 70%)" }}>
                {pack.image ? <MediaImage image={pack.image} sizes="(min-width: 1024px) 33vw, 100vw" /> : <InView className="absolute inset-4"><UniverseVisual kind="stage" /></InView>}
              </div>
              <div className="flex flex-1 flex-col p-7">
                {pack.capacity && <p className="cartel text-[var(--accent-ink)]">{pack.capacity}</p>}
                <h3 className="display mt-2 text-2xl">{pack.name}</h3>
                {pack.summary && <p className="mt-3 text-ink-muted">{pack.summary}</p>}
                {pack.contents.length > 0 && (
                  <ul className="mt-5 space-y-2 text-sm">
                    {pack.contents.map((line) => <li key={line} className="flex gap-2"><span className="text-[var(--accent-ink)]" aria-hidden>—</span>{line}</li>)}
                  </ul>
                )}
                <div className="mt-auto flex flex-wrap items-center justify-between gap-3 pt-6">
                  <span className="font-mono text-xs text-ink-muted">{pack.priceLabel}</span>
                  <AddToQuote item={{ kind: "pack", id: pack.id, name: pack.name }} compact />
                </div>
              </div>
            </Reveal>
          ))}
        </ul>
      </div>
    </Section>
  );
}

// ——— Productions du studio (écoute) ———

export function ProductionsBlock({ data }: { data: { title?: string; items?: ArtworkSummary[] } }) {
  const items = (data.items ?? []).filter((item) => item.audio?.url);
  if (items.length === 0) return null;
  return (
    <Section id="productions">
      <div style={accent("studio")}>
        <SectionTitle eyebrow="Écouter" title={data.title} />
        <ol className="divide-y divide-line rounded-[2rem] border border-line bg-night-2">
          {items.map((item, index) => {
            const track = { src: item.audio!.url, title: item.title, subtitle: "Impact Live Studio", href: `/realisations/${item.slug}`, peaks: item.audio!.peaks, accent: "var(--color-rec)" };
            return (
              <li key={item.id} className="grid items-center gap-5 p-5 sm:grid-cols-[auto_auto_1fr] sm:p-7">
                <span className="cartel hidden tabular-nums sm:block">{String(index + 1).padStart(2, "0")}</span>
                <div className="flex items-center gap-4">
                  <span className="relative grid size-16 shrink-0 place-items-center overflow-hidden rounded-xl border border-line bg-night-3" style={{ background: "radial-gradient(circle at 50% 50%, color-mix(in oklab, var(--accent) 30%, transparent), transparent 70%)" }}>
                    {item.cover ? <MediaImage image={item.cover} sizes="64px" /> : <Disc3 className="size-8 text-[var(--accent-ink)]" aria-hidden />}
                  </span>
                  <span>
                    <Link href={`/realisations/${item.slug}`} className="display block text-lg leading-tight hover:text-[var(--accent-ink)]">{item.title}</Link>
                    {item.summary && <span className="mt-1 line-clamp-1 block text-sm text-ink-muted">{item.summary}</span>}
                  </span>
                </div>
                <div className="flex items-center gap-4">
                  <PlayButton track={track} iconOnly />
                  <div className="min-w-0 flex-1 [&>*]:mt-0"><ArtworkWaveform track={track} /></div>
                </div>
              </li>
            );
          })}
        </ol>
      </div>
    </Section>
  );
}

// ——— Agenda et références ———

const dayFormat = new Intl.DateTimeFormat("fr-FR", { day: "2-digit", timeZone: "Africa/Dakar" });
const monthFormat = new Intl.DateTimeFormat("fr-FR", { month: "short", timeZone: "Africa/Dakar" });
const timeFormat = new Intl.DateTimeFormat("fr-FR", { weekday: "long", hour: "2-digit", minute: "2-digit", timeZone: "Africa/Dakar" });

export function AgendaCard({ event }: { event: AgendaEvent }) {
  const start = event.startsAt ? new Date(event.startsAt) : null;
  return (
    <Link href={`/agenda/${event.slug}`} className="group flex h-full gap-5 rounded-3xl border border-line bg-night-2 p-5 transition duration-500 hover:border-[var(--accent)]" style={accent(event.activity)}>
      <div className="grid w-20 shrink-0 place-items-center rounded-2xl border border-line bg-night py-3 text-center">
        {start ? (
          <span><span className="display block text-3xl text-[var(--accent-ink)]">{dayFormat.format(start)}</span><span className="cartel">{monthFormat.format(start).replace(".", "")}</span></span>
        ) : <span className="cartel">À venir</span>}
      </div>
      <div className="min-w-0">
        <p className="cartel text-[var(--accent-ink)]">{event.activityLabel}</p>
        <h3 className="display mt-1 text-xl leading-tight group-hover:text-[var(--accent-ink)]">{event.title}</h3>
        <p className="mt-2 text-sm text-ink-muted">{[start ? timeFormat.format(start) : null, event.venue, event.city].filter(Boolean).join(" · ")}</p>
      </div>
    </Link>
  );
}

export function AgendaBlock({ data }: { data: { title?: string; scope?: "upcoming" | "references"; items?: AgendaEvent[] } }) {
  const items = data.items ?? [];
  if (items.length === 0) return null;

  if (data.scope === "references") {
    return (
      <Section>
        <SectionTitle eyebrow="Références" title={data.title} />
        <ul className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          {items.map((event, index) => (
            <Reveal as="li" key={event.id} delay={(index % 3) * 100} className="group relative flex min-h-56 flex-col justify-end overflow-hidden rounded-[2rem] border border-line bg-night-2 p-7" >
              <div className="absolute inset-0" style={{ ...accent(event.activity), background: "radial-gradient(90% 70% at 20% 0%, color-mix(in oklab, var(--accent) 28%, transparent), transparent 70%)" }} aria-hidden />
              {event.image && <MediaImage image={event.image} sizes="(min-width: 1024px) 33vw, 100vw" className="opacity-40 transition duration-700 group-hover:scale-105" />}
              <div className="relative" style={accent(event.activity)}>
                <p className="cartel text-[var(--accent-ink)]">{[event.city, event.startsAt ? new Date(event.startsAt).getFullYear() : null].filter(Boolean).join(" · ")}</p>
                <h3 className="display mt-2 text-2xl">{event.title}</h3>
                {event.summary && <p className="mt-2 text-sm text-ink/80">{event.summary}</p>}
              </div>
            </Reveal>
          ))}
        </ul>
      </Section>
    );
  }

  return (
    <Section id="programmation">
      <div className="flex flex-wrap items-end justify-between gap-6">
        <SectionTitle eyebrow="Agenda" title={data.title} />
        <Link href="/agenda" className="group mb-12 inline-flex items-center gap-2 text-sm font-semibold text-brand">Tout l&apos;agenda <ArrowRight className="size-4 transition-transform group-hover:translate-x-1" aria-hidden /></Link>
      </div>
      <ul className="grid gap-4 md:grid-cols-2">
        {items.map((event) => <li key={event.id}><AgendaCard event={event} /></li>)}
      </ul>
    </Section>
  );
}

// ——— Formulaire de demande ———

const FORM_ANCHOR: Record<BookingType, string> = { studio_session: "reserver", space_rental: "louer", equipment_rental: "devis", event_service: "devis" };
const FORM_ACTIVITY: Record<BookingType, Activity> = { studio_session: "studio", space_rental: "space", equipment_rental: "events", event_service: "events" };

export function BookingFormBlock({ data }: { data: { title?: string; text?: string; bookingType?: BookingType } }) {
  const type = data.bookingType ?? "equipment_rental";
  return (
    <Section id={FORM_ANCHOR[type]}>
      <div className="grid gap-12 lg:grid-cols-[1fr_1.4fr]" style={accent(FORM_ACTIVITY[type])}>
        <SectionTitle eyebrow="Demande en ligne" title={data.title} text={data.text ?? "Réponse rapide de notre équipe, avec une proposition adaptée à votre projet."} />
        <LazyBookingForm type={type} />
      </div>
    </Section>
  );
}

// ——— Lieux ———

const KIND_LABEL: Record<Place["kind"], string> = { campus: "Campus EMSI", studio: "Studio", cultural_center: "Centre culturel" };

export function PlaceCard({ place }: { place: Place }) {
  const whatsapp = place.whatsapp?.replace(/[^0-9]/g, "");
  return (
    <article className="flex h-full flex-col overflow-hidden rounded-3xl border border-line bg-night-2">
      <div className="relative aspect-[16/9] border-b border-line" style={{ background: "radial-gradient(80% 70% at 60% 30%, color-mix(in oklab, var(--accent) 22%, transparent), transparent 70%)" }}>
        {place.image ? <MediaImage image={place.image} sizes="(min-width: 1024px) 33vw, 100vw" /> : (
          <InView className="absolute inset-5 opacity-80"><UniverseVisual kind={place.kind === "studio" ? "sound" : place.kind === "cultural_center" ? "design" : "stage"} /></InView>
        )}
        <span className="display absolute bottom-3 left-5 text-3xl text-ink drop-shadow-[0_2px_16px_rgb(0_0_0/0.6)]">{place.city}</span>
      </div>
      <div className="flex flex-1 flex-col p-7">
        <p className="cartel text-[var(--accent-ink)]">{KIND_LABEL[place.kind]}{place.city ? ` · ${place.city}` : ""}</p>
        <h3 className="display mt-2 text-2xl">{place.name}</h3>
        {place.description && <p className="mt-3 text-sm text-ink-muted">{place.description}</p>}
        <ul className="mt-5 space-y-2 text-sm">
          {place.address && <li className="flex gap-3"><MapPin className="mt-0.5 size-4 shrink-0 text-ink-muted" aria-hidden />{place.address}</li>}
          {place.phone && <li className="flex gap-3"><Phone className="mt-0.5 size-4 shrink-0 text-ink-muted" aria-hidden /><a href={`tel:${place.phone.replace(/[^0-9+]/g, "")}`} className="hover:text-brand">{place.phone}</a></li>}
          {place.email && <li className="flex gap-3"><Mail className="mt-0.5 size-4 shrink-0 text-ink-muted" aria-hidden /><a href={`mailto:${place.email}`} className="hover:text-brand">{place.email}</a></li>}
          {place.openingHours && <li className="flex gap-3"><Clock className="mt-0.5 size-4 shrink-0 text-ink-muted" aria-hidden />{place.openingHours}</li>}
        </ul>
        <div className="mt-auto flex flex-wrap gap-2 pt-6">
          {whatsapp && <a href={`https://wa.me/${whatsapp}`} target="_blank" rel="noopener noreferrer" className="inline-flex min-h-10 items-center rounded-full border border-line px-4 text-sm hover:border-brand">WhatsApp<span className="sr-only"> (nouvel onglet)</span></a>}
          {place.mapUrl && <a href={place.mapUrl} target="_blank" rel="noopener noreferrer" className="inline-flex min-h-10 items-center gap-2 rounded-full border border-line px-4 text-sm hover:border-brand"><MapPin className="size-4" aria-hidden />Itinéraire<span className="sr-only"> (nouvel onglet)</span></a>}
        </div>
      </div>
    </article>
  );
}

const PLACE_ACCENT: Record<Place["kind"], string> = { campus: "var(--color-brand)", studio: "var(--color-rec)", cultural_center: "var(--color-violet)" };

export function PlacesBlock({ data }: { data: { title?: string; items?: Place[] } }) {
  const items = data.items ?? [];
  if (items.length === 0) return null;
  return (
    <Section>
      <SectionTitle eyebrow="Adresses" title={data.title} />
      <ul className={cn("grid gap-5", items.length > 1 ? "md:grid-cols-2" : "max-w-xl", items.length > 2 && items.length !== 4 && "lg:grid-cols-3", items.length === 4 && "lg:grid-cols-4")}>
        {items.map((place, index) => <Reveal as="li" key={place.id} delay={(index % 3) * 100} className="h-full"><div className="h-full" style={{ ["--accent" as string]: PLACE_ACCENT[place.kind] }}><PlaceCard place={place} /></div></Reveal>)}
      </ul>
    </Section>
  );
}

