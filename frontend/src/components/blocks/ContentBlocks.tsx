import Link from "next/link";
import { ArrowRight, Award, AudioLines, Briefcase, Calendar, Clapperboard, GraduationCap, Lightbulb, MapPin, Palette, Sparkles, Users, Video } from "lucide-react";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { MediaImage } from "@/components/ui/MediaImage";
import { RichText } from "@/components/ui/RichText";
import { Section, SectionTitle } from "@/components/ui/Section";
import { VideoEmbed } from "@/components/ui/VideoEmbed";
import { cn } from "@/lib/utils";
import type { CardsData, CtaData, FaqData, GalleryData, QuoteData, StatsData, TextData, TextImageData, TimelineData, VideoData } from "./types";

const ICONS = { "audio-lines": AudioLines, lightbulb: Lightbulb, video: Video, palette: Palette, clapperboard: Clapperboard, "graduation-cap": GraduationCap, users: Users, award: Award, calendar: Calendar, "map-pin": MapPin, briefcase: Briefcase, sparkles: Sparkles } as const;

export function TextBlock({ data }: { data: TextData }) {
  return (
    <Section>
      <div className="mx-auto max-w-3xl">
        {data.title && <h2 className="mb-8 font-display text-3xl sm:text-4xl">{data.title}</h2>}
        <RichText html={data.body} className="text-lg" />
      </div>
    </Section>
  );
}

export function TextImageBlock({ data }: { data: TextImageData }) {
  return (
    <Section>
      <div className={cn("grid items-center gap-12 lg:grid-cols-2", data.imagePosition === "left" && "lg:[&>*:first-child]:order-2")}>
        <div>
          {data.title && <h2 className="mb-6 font-display text-3xl sm:text-4xl">{data.title}</h2>}
          <RichText html={data.body} className="text-lg" />
        </div>
        <div className="relative aspect-[4/3] overflow-hidden rounded-3xl border border-line">
          <MediaImage image={data.image} sizes="(min-width: 1024px) 50vw, 100vw" />
        </div>
      </div>
    </Section>
  );
}

export function GalleryBlock({ data }: { data: GalleryData }) {
  const images = (data.images ?? []).filter((item) => item.image);
  return (
    <Section>
      <SectionTitle title={data.title} />
      <ul className={cn("grid gap-4", data.layout === "mosaic" ? "grid-cols-2 lg:grid-cols-4 [&>li:nth-child(5n+1)]:col-span-2 [&>li:nth-child(5n+1)]:row-span-2" : "sm:grid-cols-2 lg:grid-cols-3")}>
        {images.map((item, index) => (
          <li key={index}>
            <figure>
              <a href={item.image!.url} target="_blank" rel="noopener noreferrer" className="relative block aspect-square overflow-hidden rounded-2xl border border-line">
                <MediaImage image={item.image} sizes="(min-width: 1024px) 33vw, 50vw" className="transition duration-700 hover:scale-105" />
                <span className="sr-only">Agrandir l&apos;image (nouvel onglet)</span>
              </a>
              {item.caption && <figcaption className="cartel mt-2">{item.caption}</figcaption>}
            </figure>
          </li>
        ))}
      </ul>
    </Section>
  );
}

export function VideoBlock({ data }: { data: VideoData }) {
  return (
    <Section>
      <div className="mx-auto max-w-5xl">
        <SectionTitle title={data.title} />
        <VideoEmbed url={data.url} title={data.title ?? "Vidéo"} poster={data.poster?.url} />
        {data.caption && <p className="cartel mt-3">{data.caption}</p>}
        {data.transcript && (
          <details className="mt-4 rounded-xl border border-line p-4 text-sm text-ink-muted">
            <summary className="cursor-pointer text-ink">Transcription</summary>
            <p className="mt-3 whitespace-pre-line">{data.transcript}</p>
          </details>
        )}
      </div>
    </Section>
  );
}

export function StatsBlock({ data }: { data: StatsData }) {
  return (
    <Section>
      <SectionTitle title={data.title} />
      <dl className="grid gap-px overflow-hidden rounded-3xl border border-line bg-line sm:grid-cols-2 lg:grid-cols-3">
        {(data.items ?? []).map((item) => (
          <div key={item.label} className="bg-night p-8">
            <dt className="cartel">{item.label}</dt>
            <dd className="mt-3 font-display text-5xl text-[var(--accent)]">{item.value}</dd>
            {item.detail && <dd className="mt-2 text-sm text-ink-muted">{item.detail}</dd>}
          </div>
        ))}
      </dl>
    </Section>
  );
}

export function QuoteBlock({ data }: { data: QuoteData }) {
  return (
    <Section>
      <figure className="mx-auto max-w-4xl text-center">
        <blockquote className="font-display text-3xl leading-snug italic text-balance sm:text-4xl">« {data.text} »</blockquote>
        {(data.author || data.role) && (
          <figcaption className="mt-8 flex items-center justify-center gap-4">
            {data.photo && (
              <span className="relative size-14 overflow-hidden rounded-full border border-line">
                <MediaImage image={data.photo} sizes="56px" />
              </span>
            )}
            <span className="text-left">
              {data.author && <span className="block font-medium">{data.author}</span>}
              {data.role && <span className="cartel">{data.role}</span>}
            </span>
          </figcaption>
        )}
      </figure>
    </Section>
  );
}

export function CtaBlock({ data }: { data: CtaData }) {
  return (
    <Section>
      <div className="beam relative overflow-hidden rounded-[2rem] border border-line bg-night-2 px-6 py-16 text-center sm:px-16">
        <h2 className="mx-auto max-w-3xl font-display text-4xl text-balance sm:text-5xl">{data.title}</h2>
        {data.text && <p className="mx-auto mt-4 max-w-2xl text-lg text-ink-muted">{data.text}</p>}
        <div className="mt-10 flex flex-wrap justify-center gap-3">
          {(data.buttons ?? []).map((button) => (
            <ButtonLink key={button.url + button.label} href={button.url} variant={button.style === "secondary" ? "secondary" : "primary"}>{button.label}</ButtonLink>
          ))}
        </div>
      </div>
    </Section>
  );
}

export function CardsBlock({ data }: { data: CardsData }) {
  return (
    <Section>
      <SectionTitle title={data.title} />
      <ul className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        {(data.items ?? []).map((item) => {
          const Icon = item.icon ? ICONS[item.icon as keyof typeof ICONS] : null;
          const body = (
            <>
              {Icon && <Icon className="size-7 text-[var(--accent)]" aria-hidden />}
              <h3 className="mt-5 font-display text-2xl">{item.title}</h3>
              {item.text && <p className="mt-3 text-ink-muted">{item.text}</p>}
              {item.url && <span className="mt-5 inline-flex items-center gap-2 text-sm font-medium text-[var(--accent)]">En savoir plus <ArrowRight className="size-4" aria-hidden /></span>}
            </>
          );
          return (
            <li key={item.title} className="h-full">
              {item.url ? (
                <Link href={item.url} className="block h-full rounded-3xl border border-line bg-night-2 p-8 transition hover:border-[var(--accent)]">{body}</Link>
              ) : (
                <div className="h-full rounded-3xl border border-line bg-night-2 p-8">{body}</div>
              )}
            </li>
          );
        })}
      </ul>
    </Section>
  );
}

export function TimelineBlock({ data }: { data: TimelineData }) {
  return (
    <Section>
      <SectionTitle title={data.title} />
      <ol className="relative space-y-10 border-l border-line pl-8">
        {(data.steps ?? []).map((step, index) => (
          <li key={index} className="relative">
            <span className="absolute -left-[2.3rem] top-1.5 size-3 rounded-full bg-[var(--accent)] shadow-[0_0_20px_var(--accent)]" aria-hidden />
            <p className="cartel">{step.period}{step.tag && <span className="ml-3 rounded-full border border-line px-2 py-0.5">{step.tag}</span>}</p>
            <h3 className="mt-2 font-display text-2xl">{step.title}</h3>
            {step.text && <p className="mt-2 max-w-2xl text-ink-muted">{step.text}</p>}
          </li>
        ))}
      </ol>
    </Section>
  );
}

export function FaqBlock({ data }: { data: FaqData }) {
  const items = data.items ?? [];
  if (items.length === 0) return null;
  return (
    <Section>
      <div className="mx-auto max-w-3xl">
        <SectionTitle title={data.title ?? "Questions fréquentes"} />
        <div className="divide-y divide-line border-y border-line">
          {items.map((item) => (
            <details key={item.question} className="group py-5">
              <summary className="flex cursor-pointer list-none items-center justify-between gap-6 font-medium">
                {item.question}
                <span className="text-[var(--accent)] transition group-open:rotate-45" aria-hidden>+</span>
              </summary>
              <p className="mt-4 whitespace-pre-line text-ink-muted">{item.answer}</p>
            </details>
          ))}
        </div>
      </div>
    </Section>
  );
}
