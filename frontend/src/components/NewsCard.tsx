import Link from "next/link";
import type { NewsItem } from "@/lib/types";
import { MediaImage } from "@/components/ui/MediaImage";
import { formatDate } from "@/lib/utils";

export function NewsCard({ news }: { news: NewsItem }) {
  return (
    <article className="group">
      <Link href={`/actualites/${news.slug}`} className="block">
        <div className="relative aspect-[16/10] overflow-hidden rounded-2xl border border-line bg-night-3">
          {news.image ? (
            <MediaImage image={news.image} sizes="(min-width: 768px) 33vw, 100vw" className="transition duration-700 group-hover:scale-105" />
          ) : (
            <div className="absolute inset-0 grid place-items-center bg-[radial-gradient(80%_70%_at_30%_20%,color-mix(in_oklab,var(--color-violet)_35%,transparent),transparent_70%)]" aria-hidden>
              <span className="display text-5xl text-ink/15">EMSI</span>
            </div>
          )}
        </div>
        {news.publishedAt && <time dateTime={news.publishedAt} className="cartel mt-4 block">{formatDate(news.publishedAt)}</time>}
        <h3 className="display mt-2 text-xl leading-snug group-hover:text-brand">{news.title}</h3>
        {news.excerpt && <p className="mt-2 line-clamp-2 text-sm text-ink-muted">{news.excerpt}</p>}
      </Link>
    </article>
  );
}
