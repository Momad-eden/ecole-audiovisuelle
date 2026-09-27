import type { Metadata } from "next";
import Link from "next/link";
import { notFound } from "next/navigation";
import { ArrowLeft, CalendarDays, MapPin } from "lucide-react";
import { ACTIVITY_ACCENT } from "@/components/blocks/ImpactBlocks";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { MediaImage } from "@/components/ui/MediaImage";
import { RichText } from "@/components/ui/RichText";
import { api } from "@/lib/api";
import { siteUrl } from "@/lib/utils";

type Props = { params: Promise<{ slug: string }> };

const dateFormat = new Intl.DateTimeFormat("fr-FR", { dateStyle: "full", timeStyle: "short", timeZone: "Africa/Dakar" });

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const event = await api.agendaEvent((await params).slug);
  return event ? { title: event.title, description: event.summary ?? undefined } : {};
}

export default async function AgendaEventPage({ params }: Props) {
  const event = await api.agendaEvent((await params).slug);
  if (!event) notFound();

  const jsonLd = event.startsAt ? {
    "@context": "https://schema.org",
    "@type": "Event",
    name: event.title,
    startDate: event.startsAt,
    endDate: event.endsAt ?? undefined,
    description: event.summary ?? undefined,
    image: event.image?.url,
    url: `${siteUrl}/agenda/${event.slug}`,
    location: { "@type": "Place", name: event.venue ?? event.city ?? "Saint-Louis", address: { "@type": "PostalAddress", addressLocality: event.city ?? undefined, addressCountry: "SN" } },
  } : null;

  return (
    <article className="mx-auto max-w-5xl px-4 pb-24 pt-32 sm:px-6 lg:px-8" style={{ ["--accent" as string]: ACTIVITY_ACCENT[event.activity] }}>
      <Link href="/agenda" className="cartel inline-flex items-center gap-2 hover:text-ink"><ArrowLeft className="size-4" aria-hidden /> Agenda</Link>
      <p className="cartel mt-8 text-[var(--accent-ink)]">{event.activityLabel}</p>
      <h1 className="display mt-3 text-[clamp(2.4rem,6vw,4.8rem)] text-balance">{event.title}</h1>
      <ul className="mt-6 flex flex-wrap gap-x-6 gap-y-2 text-ink/85">
        {event.startsAt && <li className="flex items-center gap-2"><CalendarDays className="size-4 text-[var(--accent-ink)]" aria-hidden />{dateFormat.format(new Date(event.startsAt))}</li>}
        {(event.venue || event.city) && <li className="flex items-center gap-2"><MapPin className="size-4 text-[var(--accent-ink)]" aria-hidden />{[event.venue, event.city].filter(Boolean).join(", ")}</li>}
      </ul>
      {event.ticketUrl && <div className="mt-8"><ButtonLink href={event.ticketUrl} size="lg">Réserver ma place</ButtonLink></div>}
      {event.image && <div className="relative mt-12 aspect-[16/9] overflow-hidden rounded-[2rem] border border-line"><MediaImage image={event.image} sizes="(min-width: 1024px) 64rem, 100vw" priority /></div>}
      {event.summary && <p className="mt-10 text-xl text-ink/85">{event.summary}</p>}
      {event.content && <RichText html={event.content} className="mt-8 text-lg" />}
      {jsonLd && <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd).replace(/</g, "\\u003c") }} />}
    </article>
  );
}
