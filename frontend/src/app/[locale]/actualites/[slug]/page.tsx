import { openGraphBase } from "@/lib/metadata";
import type { Metadata } from "next";
import { LocaleLink } from "@/components/i18n/LocaleLink";
import { notFound } from "next/navigation";
import { ArrowLeft } from "lucide-react";
import { MediaImage } from "@/components/ui/MediaImage";
import { RichText } from "@/components/ui/RichText";
import { ShareButtons } from "@/components/ui/ShareButtons";
import { api } from "@/lib/api";
import { getDictionary } from "@/lib/i18n";
import { formatDate } from "@/lib/i18n/format";
import type { Locale } from "@/lib/i18n/locales";
import { frenchSpacing } from "@/lib/utils";
import { localeAlternates } from "@/lib/i18n/alternates";
import { asLocale } from "@/lib/i18n/locales";

type Props = { params: Promise<{ locale: Locale; slug: string }> };

async function baseMetadata({ params }: Props): Promise<Metadata> {
  const { locale, slug } = await params;
  const news = await api.newsItem(slug, locale);
  if (!news) return {};
  return { title: news.title, description: news.excerpt ?? undefined, openGraph: { ...openGraphBase(asLocale(locale)), type: "article", publishedTime: news.publishedAt ?? undefined, ...(news.image ? { images: [{ url: news.image.url }] } : {}) } };
}

export async function generateMetadata(props: Props): Promise<Metadata> {
  const p = await props.params;
  return { ...(await baseMetadata(props)), alternates: localeAlternates(`/actualites/${p.slug}`, asLocale(p.locale)) };
}

export default async function NewsItemPage({ params }: Props) {
  const { locale, slug } = await params;
  const news = await api.newsItem(slug, locale);
  if (!news) notFound();

  return (
    <article className="mx-auto max-w-3xl px-4 pt-36 sm:px-6">
      <LocaleLink href="/actualites" className="cartel inline-flex items-center gap-2 hover:text-ink"><ArrowLeft className="size-4" aria-hidden /> {getDictionary(locale).news.title}</LocaleLink>
      {news.publishedAt && <time dateTime={news.publishedAt} className="cartel mt-8 block">{formatDate(news.publishedAt, locale)}</time>}
      <h1 className="display mt-4 text-[clamp(2.2rem,5vw,3.8rem)] text-balance">{frenchSpacing(news.title)}</h1>
      {news.excerpt && <p className="mt-6 text-xl text-ink/80">{news.excerpt}</p>}
      {news.image && (
        <div className="relative mt-10 aspect-[16/9] overflow-hidden rounded-3xl border border-line">
          <MediaImage image={news.image} sizes="(min-width: 768px) 768px, 100vw" priority />
        </div>
      )}
      <RichText html={news.content} locale={locale} className="mt-10 text-lg" />
      <div className="mt-14 border-t border-line pt-8 pb-24"><ShareButtons path={`/actualites/${news.slug}`} title={news.title} /></div>
    </article>
  );
}
