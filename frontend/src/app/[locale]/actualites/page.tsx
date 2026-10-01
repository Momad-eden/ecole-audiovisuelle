import type { Metadata } from "next";
import { LocaleLink } from "@/components/i18n/LocaleLink";
import { NewsCard } from "@/components/NewsCard";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { EmptyState } from "@/components/ui/EmptyState";
import { PageHeader } from "@/components/ui/PageHeader";
import { Section } from "@/components/ui/Section";
import { api } from "@/lib/api";
import { getDictionary } from "@/lib/i18n";
import type { Locale } from "@/lib/i18n/locales";
import { localeAlternates } from "@/lib/i18n/alternates";
import { asLocale } from "@/lib/i18n/locales";

type Props = { params: Promise<{ locale: Locale }>; searchParams: Promise<{ page?: string }> };

async function baseMetadata({ params }: Props): Promise<Metadata> {
  return getDictionary((await params).locale).meta.news;
}

export async function generateMetadata(props: Props): Promise<Metadata> {
  const p = await props.params;
  return { ...(await baseMetadata(props)), alternates: localeAlternates("/actualites", asLocale(p.locale)) };
}

export default async function NewsPage({ params, searchParams }: Props) {
  const { locale } = await params;
  const page = Math.max(1, Number((await searchParams).page ?? 1) || 1);
  const news = await api.news(page, locale);
  const { news: t, common } = getDictionary(locale);

  return (
    <>
      <PageHeader eyebrow={t.eyebrow} title={t.title} text={t.text} />
      <Section className="pt-0 sm:pt-0">
        {news.data.length === 0 ? (
          <EmptyState title={t.emptyTitle} text={t.emptyText}>
            <ButtonLink href="/maison-habib-faye/agenda">{t.seeAgenda}</ButtonLink>
            <ButtonLink href="/emsi" variant="secondary">{common.discoverUniverses}</ButtonLink>
          </EmptyState>
        ) : (
          <div className="grid gap-x-8 gap-y-14 md:grid-cols-2 lg:grid-cols-3">
            {news.data.map((item) => <NewsCard key={item.id} news={item} locale={locale} />)}
          </div>
        )}
        {news.meta.last_page > 1 && (
          <nav aria-label={t.pagination} className="mt-16 flex justify-center gap-3">
            {page > 1 && <LocaleLink href={`/actualites?page=${page - 1}`} className="inline-flex min-h-11 items-center rounded-full border border-line px-5 hover:border-ink/40">{t.previous}</LocaleLink>}
            <span className="cartel self-center">{t.page(page, news.meta.last_page)}</span>
            {page < news.meta.last_page && <LocaleLink href={`/actualites?page=${page + 1}`} className="inline-flex min-h-11 items-center rounded-full border border-line px-5 hover:border-ink/40">{t.next}</LocaleLink>}
          </nav>
        )}
      </Section>
    </>
  );
}
