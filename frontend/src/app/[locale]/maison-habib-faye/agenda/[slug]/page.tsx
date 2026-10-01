import type { Metadata } from "next";
import { LocaleLink } from "@/components/i18n/LocaleLink";
import { notFound } from "next/navigation";
import { ArrowLeft, CalendarDays, MapPin } from "lucide-react";
import { ACTIVITY_ACCENT } from "@/components/blocks/ImpactBlocks";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { MediaImage } from "@/components/ui/MediaImage";
import { RichText } from "@/components/ui/RichText";
import { ShareButtons } from "@/components/ui/ShareButtons";
import { api } from "@/lib/api";
import { getDictionary } from "@/lib/i18n";
import { formatDate } from "@/lib/i18n/format";
import type { Locale } from "@/lib/i18n/locales";
import { CurrentCrumb } from "@/components/layout/domain-crumb";
import { DomainChrome } from "@/components/layout/DomainChrome";
import { siteUrl } from "@/lib/utils";

type Props = { params: Promise<{ locale: Locale; slug: string }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { locale, slug } = await params;
  const event = await api.agendaEvent(slug, locale);
  return event ? { title: event.title, description: event.summary ?? undefined } : {};
}

export default async function AgendaEventPage({ params }: Props) {
  const { locale, slug } = await params;
  const event = await api.agendaEvent(slug, locale);
  if (!event) notFound();

  const jsonLd = event.startsAt ? {
    "@context": "https://schema.org",
    "@type": "Event",
    name: event.title,
    startDate: event.startsAt,
    endDate: event.endsAt ?? undefined,
    description: event.summary ?? undefined,
    image: event.image?.url,
    url: `${siteUrl}/maison-habib-faye/agenda/${event.slug}`,
    location: { "@type": "Place", name: event.venue ?? event.city ?? "Saint-Louis", address: { "@type": "PostalAddress", addressLocality: event.city ?? undefined, addressCountry: "SN" } },
  } : null;

  const { menus, domains } = await api.site(locale);
  const t = getDictionary(locale).agenda;

  return (
    <DomainChrome domain="maison" site={{ menus, domains }} path={`/maison-habib-faye/agenda/${event.slug}`} title={event.title}>
    <CurrentCrumb title={event.title} />
    <article className="mx-auto max-w-5xl px-4 pb-24 pt-[calc(var(--chrome-h)+3.5rem)] sm:px-6 lg:px-8" style={{ ["--accent" as string]: ACTIVITY_ACCENT[event.activity] }}>
      <LocaleLink href="/maison-habib-faye/agenda" className="cartel inline-flex items-center gap-2 hover:text-ink"><ArrowLeft className="size-4" aria-hidden /> {t.back}</LocaleLink>
      <p className="cartel mt-8 text-[var(--accent-ink)]">{event.activityLabel}</p>
      <h1 className="display mt-3 text-[clamp(2.4rem,6vw,4.8rem)] text-balance">{event.title}</h1>
      <ul className="mt-6 flex flex-wrap gap-x-6 gap-y-2 text-ink/85">
        {event.startsAt && <li className="flex items-center gap-2"><CalendarDays className="size-4 text-[var(--accent-ink)]" aria-hidden />{formatDate(event.startsAt, locale, { dateStyle: "full", timeStyle: "short" })}</li>}
        {(event.venue || event.city) && <li className="flex items-center gap-2"><MapPin className="size-4 text-[var(--accent-ink)]" aria-hidden />{[event.venue, event.city].filter(Boolean).join(", ")}</li>}
      </ul>
      {event.ticketUrl && <div className="mt-8"><ButtonLink href={event.ticketUrl} size="lg">{t.book}</ButtonLink></div>}
      {event.image && <div className="relative mt-12 aspect-[16/9] overflow-hidden rounded-[2rem] border border-line"><MediaImage image={event.image} sizes="(min-width: 1024px) 64rem, 100vw" priority /></div>}
      {event.summary && <p className="mt-10 text-xl text-ink/85">{event.summary}</p>}
      {event.content && <RichText html={event.content} locale={locale} className="mt-8 text-lg" />}
      <div className="mt-14 border-t border-line pt-8"><ShareButtons path={`/maison-habib-faye/agenda/${event.slug}`} title={event.title} /></div>
      {jsonLd && <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd).replace(/</g, "\\u003c") }} />}
    </article>
    </DomainChrome>
  );
}
