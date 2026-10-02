import type { Metadata } from "next";
import { LocaleLink } from "@/components/i18n/LocaleLink";
import { ArtworkGrid } from "@/components/museum/ArtworkCard";
import { MediaImage } from "@/components/ui/MediaImage";
import { PageHeader } from "@/components/ui/PageHeader";
import { Section, SectionTitle } from "@/components/ui/Section";
import { api } from "@/lib/api";
import { getDictionary } from "@/lib/i18n";
import type { Locale } from "@/lib/i18n/locales";
import { cn } from "@/lib/utils";
import { localeAlternates } from "@/lib/i18n/alternates";
import { asLocale } from "@/lib/i18n/locales";

type Props = { params: Promise<{ locale: Locale }>; searchParams: Promise<{ univers?: string }> };

async function baseMetadata({ params }: Props): Promise<Metadata> {
  return getDictionary((await params).locale).meta.artworks;
}

export async function generateMetadata(props: Props): Promise<Metadata> {
  const p = await props.params;
  return { ...(await baseMetadata(props)), alternates: localeAlternates("/emsi/realisations", asLocale(p.locale)) };
}

export default async function RealisationsPage({ params, searchParams }: Props) {
  const { locale } = await params;
  const { univers } = await searchParams;
  const [universes, artworks] = await Promise.all([
    api.rooms(locale),
    api.artworks(`perPage=24${univers ? `&room=${encodeURIComponent(univers)}` : ""}`, locale),
  ]);
  const active = universes.find((u) => u.slug === univers);
  const t = getDictionary(locale).artworksPage;

  return (
    <>
      <PageHeader eyebrow={t.eyebrow} title={t.title} text={t.text} accent={active?.accentColor} />

      <Section className="pt-0 sm:pt-0">
        <nav aria-label={t.filter} className="mb-12">
          <ul className="flex flex-wrap gap-2">
            <li>
              <LocaleLink href="/emsi/realisations" aria-current={!active ? "page" : undefined} className={cn("inline-flex min-h-11 items-center rounded-full border px-5 text-sm transition", !active ? "border-brand bg-brand text-on-accent" : "border-line hover:border-ink/40")}>{t.all}</LocaleLink>
            </li>
            {universes.filter((u) => !u.isUpcoming).map((universe) => {
              const selected = universe.slug === active?.slug;
              return (
                <li key={universe.id}>
                  <LocaleLink
                    href={`/emsi/realisations?univers=${universe.slug}`}
                    aria-current={selected ? "page" : undefined}
                    className={cn("inline-flex min-h-11 items-center gap-2 rounded-full border px-5 text-sm transition", selected ? "border-[var(--accent)] bg-[var(--accent-ink)] text-on-accent" : "border-line hover:border-[var(--accent)]")}
                    style={{ ["--accent" as string]: universe.accentColor }}
                  >
                    {!selected && <span className="size-2 rounded-full bg-[var(--accent-ink)]" aria-hidden />}
                    {universe.name}
                  </LocaleLink>
                </li>
              );
            })}
          </ul>
        </nav>
        <ArtworkGrid artworks={artworks.data} empty={active ? t.emptyUniverse(active.name) : undefined} locale={locale} />
      </Section>
    </>
  );
}
