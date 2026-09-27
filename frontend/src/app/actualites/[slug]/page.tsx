import type { Metadata } from "next";
import Link from "next/link";
import { notFound } from "next/navigation";
import { ArrowLeft } from "lucide-react";
import { MediaImage } from "@/components/ui/MediaImage";
import { RichText } from "@/components/ui/RichText";
import { api } from "@/lib/api";
import { formatDate } from "@/lib/utils";

type Props = { params: Promise<{ slug: string }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const news = await api.newsItem((await params).slug);
  if (!news) return {};
  return { title: news.title, description: news.excerpt ?? undefined, openGraph: { type: "article", publishedTime: news.publishedAt ?? undefined, images: news.image ? [{ url: news.image.url }] : undefined } };
}

export default async function NewsItemPage({ params }: Props) {
  const news = await api.newsItem((await params).slug);
  if (!news) notFound();

  return (
    <article className="mx-auto max-w-3xl px-4 pt-16 sm:px-6">
      <Link href="/actualites" className="cartel inline-flex items-center gap-2 hover:text-ink"><ArrowLeft className="size-4" aria-hidden /> Actualités</Link>
      {news.publishedAt && <time dateTime={news.publishedAt} className="cartel mt-8 block">{formatDate(news.publishedAt)}</time>}
      <h1 className="mt-3 font-display text-4xl leading-tight text-balance sm:text-5xl">{news.title}</h1>
      {news.excerpt && <p className="mt-6 text-xl text-ink/80">{news.excerpt}</p>}
      {news.image && (
        <div className="relative mt-10 aspect-[16/9] overflow-hidden rounded-3xl border border-line">
          <MediaImage image={news.image} sizes="(min-width: 768px) 768px, 100vw" priority />
        </div>
      )}
      <RichText html={news.content} className="mt-10 text-lg" />
    </article>
  );
}
