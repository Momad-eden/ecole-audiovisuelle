import Link from "next/link";
import type { NewsItem } from "@/lib/types";
import { MediaImage } from "@/components/ui/MediaImage";
import { formatDate } from "@/lib/utils";

export function NewsCard({ news }: { news: NewsItem }) {
  return (
    <article className="group">
      <Link href={`/actualites/${news.slug}`} className="block">
        <div className="relative aspect-[16/10] overflow-hidden rounded-2xl border border-line bg-night-3">
          <MediaImage image={news.image} sizes="(min-width: 768px) 33vw, 100vw" className="transition duration-700 group-hover:scale-105" />
        </div>
        {news.publishedAt && <time dateTime={news.publishedAt} className="cartel mt-4 block">{formatDate(news.publishedAt)}</time>}
        <h3 className="mt-2 font-display text-xl leading-snug group-hover:text-amber">{news.title}</h3>
        {news.excerpt && <p className="mt-2 line-clamp-2 text-sm text-ink-muted">{news.excerpt}</p>}
      </Link>
    </article>
  );
}
