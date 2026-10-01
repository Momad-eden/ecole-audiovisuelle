import { LocaleLink } from "@/components/i18n/LocaleLink";
import { Emphasis } from "@/components/ui/Emphasis";
import { ArrowRight, Award, AudioLines, Briefcase, Calendar, Clapperboard, GraduationCap, Lightbulb, MapPin, Palette, Plus, Sparkles, Users, Video } from "lucide-react";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { MediaImage } from "@/components/ui/MediaImage";
import { RichText } from "@/components/ui/RichText";
import { Section, SectionTitle } from "@/components/ui/Section";
import { VideoEmbed } from "@/components/ui/VideoEmbed";
import { CountUp } from "@/components/motion/CountUp";
import { Reveal } from "@/components/motion/Reveal";
import { getDictionary } from "@/lib/i18n";
import type { Locale } from "@/lib/i18n/locales";
import { cn } from "@/lib/utils";
import type { BlockProps, CardsData, CtaData, FaqData, GalleryData, QuoteData, StatsData, TextData, TextImageData, TimelineData, VideoData } from "./types";

const ICONS = { "audio-lines": AudioLines, lightbulb: Lightbulb, video: Video, palette: Palette, clapperboard: Clapperboard, "graduation-cap": GraduationCap, users: Users, award: Award, calendar: Calendar, "map-pin": MapPin, briefcase: Briefcase, sparkles: Sparkles } as const;

export function TextBlock({ data, locale }: BlockProps<TextData>) {
  return (
    <Section>
      <Reveal className="mx-auto grid max-w-6xl gap-10 lg:grid-cols-[1fr_2fr]">
        {data.title ? <h2 className="display text-[clamp(1.9rem,3.6vw,3rem)] text-balance"><Emphasis text={data.title} /></h2> : <span />}
        <RichText html={data.body} locale={locale} className="text-lg" />
      </Reveal>
    </Section>
  );
}

export function TextImageBlock({ data, locale }: BlockProps<TextImageData>) {
  return (
    <Section>
      <div className={cn("grid items-center gap-12 lg:grid-cols-2", data.imagePosition === "left" && "lg:[&>*:first-child]:order-2")}>
        <Reveal>
          {data.title && <h2 className="display mb-6 text-[clamp(1.9rem,3.6vw,3rem)] text-balance">{data.title}</h2>}
          <RichText html={data.body} locale={locale} className="text-lg" />
        </Reveal>
        <Reveal delay={120} className="relative aspect-[4/3] overflow-hidden rounded-[2rem] border border-line">
          <MediaImage image={data.image} sizes="(min-width: 1024px) 50vw, 100vw" />
        </Reveal>
      </div>
    </Section>
  );
}

export function GalleryBlock({ data, locale }: BlockProps<GalleryData>) {
  const t = getDictionary(locale).blocks;
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
                <span className="sr-only">{t.enlargeImage}</span>
              </a>
              {item.caption && <figcaption className="cartel mt-2">{item.caption}</figcaption>}
            </figure>
          </li>
        ))}
      </ul>
    </Section>
  );
}

export function VideoBlock({ data, locale }: BlockProps<VideoData>) {
  const t = getDictionary(locale).blocks;
  return (
    <Section>
      <div className="mx-auto max-w-5xl">
        <SectionTitle title={data.title} />
        <VideoEmbed url={data.url} title={data.title ?? t.video} poster={data.poster?.url} />
        {data.caption && <p className="cartel mt-3">{data.caption}</p>}
        {data.transcript && (
          <details className="mt-4 rounded-xl border border-line p-4 text-sm text-ink-muted">
            <summary className="cursor-pointer text-ink">{t.transcript}</summary>
            <p className="mt-3 whitespace-pre-line">{data.transcript}</p>
          </details>
        )}
      </div>
    </Section>
  );
}

export function StatsBlock({ data }: BlockProps<StatsData>) {
  return (
    <Section>
      <SectionTitle title={data.title} />
      <dl className={cn("grid border-y border-line sm:grid-cols-2", (data.items ?? []).length === 3 && "lg:grid-cols-3", (data.items ?? []).length >= 4 && "lg:grid-cols-4")}>
        {(data.items ?? []).map((item, i) => (
          <Reveal key={item.label} delay={i * 100} className={cn("relative flex flex-col py-10 sm:px-8 sm:py-12", i > 0 && "border-t border-line sm:border-t-0 sm:border-l")}>
            <dt className="cartel order-2 mt-5 text-ink">{item.label}</dt>
            <dd className="display order-1 text-[clamp(3.6rem,8vw,7rem)] leading-none text-[var(--accent-ink)]">
              <CountUp value={item.value} />
            </dd>
            {item.detail && <dd className="order-3 mt-2 max-w-xs text-sm text-ink-muted">{item.detail}</dd>}
          </Reveal>
        ))}
      </dl>
    </Section>
  );
}

export function QuoteBlock({ data, locale }: BlockProps<QuoteData>) {
  const t = getDictionary(locale).blocks;
  return (
    <Section>
      <Reveal as="figure" className="mx-auto max-w-5xl">
        <span className="display block text-8xl leading-none text-[var(--accent-ink)]" aria-hidden>{t.quoteMark}</span>
        <blockquote className="display -mt-6 text-[clamp(1.8rem,4vw,3.2rem)] text-balance">{data.text}</blockquote>
        {(data.author || data.role) && (
          <figcaption className="mt-10 flex items-center gap-4">
            {data.photo && (
              <span className="relative size-14 overflow-hidden rounded-full border border-line">
                <MediaImage image={data.photo} sizes="56px" />
              </span>
            )}
            <span>
              {data.author && <span className="block font-semibold">{data.author}</span>}
              {data.role && <span className="cartel">{data.role}</span>}
            </span>
          </figcaption>
        )}
      </Reveal>
    </Section>
  );
}

/** Appel à l'action final : un plateau illuminé. */
export function CtaBlock({ data }: { data: CtaData; locale?: Locale }) {
  return (
    <section className="relative isolate overflow-hidden px-4 py-28 sm:px-6 sm:py-36 lg:px-8">
      <div className="absolute inset-0 -z-10" aria-hidden>
        <div className="stage-beam" style={{ left: "10%", ["--beam" as string]: "var(--color-brand)", ["--from" as string]: "-14deg", ["--to" as string]: "12deg" }} />
        <div className="stage-beam" style={{ left: "52%", ["--beam" as string]: "var(--color-violet)", ["--from" as string]: "12deg", ["--to" as string]: "-10deg", ["--sweep" as string]: "9s" }} />
        <div className="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-night to-transparent" />
      </div>
      <Reveal className="mx-auto max-w-5xl text-center">
        <h2 className="display text-[clamp(2.4rem,5.6vw,5rem)] text-balance"><Emphasis text={data.title} /></h2>
        {data.text && <p className="mx-auto mt-6 max-w-2xl text-lg text-ink/80 sm:text-xl">{data.text}</p>}
        <div className="mt-12 flex flex-wrap justify-center gap-3">
          {(data.buttons ?? []).map((button) => (
            <ButtonLink key={button.url + button.label} href={button.url} size="lg" variant={button.style === "secondary" ? "secondary" : "primary"}>{button.label}</ButtonLink>
          ))}
        </div>
      </Reveal>
    </section>
  );
}

export function CardsBlock({ data, locale }: BlockProps<CardsData>) {
  const t = getDictionary(locale).common;
  return (
    <Section>
      <SectionTitle title={data.title} />
      <ul className="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        {(data.items ?? []).map((item, index) => {
          const Icon = item.icon ? ICONS[item.icon as keyof typeof ICONS] : null;
          const body = (
            <>
              <div className="flex items-center justify-between">
                {Icon ? <Icon className="size-7 text-[var(--accent-ink)]" aria-hidden /> : <span />}
                <span className="cartel tabular-nums">{String(index + 1).padStart(2, "0")}</span>
              </div>
              <h3 className="display mt-8 text-2xl">{item.title}</h3>
              {item.text && <p className="mt-3 text-ink-muted">{item.text}</p>}
              {item.url && <span className="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-[var(--accent-ink)]">{t.learnMore} <ArrowRight className="size-4 transition-transform group-hover:translate-x-1" aria-hidden /></span>}
            </>
          );
          return (
            <Reveal as="li" key={item.title} delay={(index % 3) * 100} className="h-full">
              {item.url ? (
                <LocaleLink href={item.url} className="group block h-full rounded-3xl border border-line bg-night-2 p-8 transition duration-500 hover:-translate-y-1 hover:border-[var(--accent)]">{body}</LocaleLink>
              ) : (
                <div className="h-full rounded-3xl border border-line bg-night-2 p-8">{body}</div>
              )}
            </Reveal>
          );
        })}
      </ul>
    </Section>
  );
}

export function TimelineBlock({ data, locale }: BlockProps<TimelineData>) {
  const t = getDictionary(locale).blocks;
  const steps = data.steps ?? [];
  if (data.layout === "steps") {
    return (
      <Section>
        <SectionTitle eyebrow={t.howTo} title={data.title} />
        <ol className="relative grid gap-5 md:grid-cols-2 lg:grid-cols-4">
          <span className="absolute left-0 right-0 top-[3.4rem] hidden h-px bg-gradient-to-r from-brand via-violet to-hmi opacity-60 lg:block" aria-hidden />
          {steps.map((step, index) => (
            <Reveal as="li" key={index} delay={index * 120} className="relative rounded-3xl border border-line bg-night-2 p-7">
              <p className="display text-outline text-6xl tabular-nums" aria-hidden>{step.period}</p>
              <span className="relative -mt-3 mb-5 block size-3 rounded-full bg-brand shadow-[0_0_18px_var(--color-brand)]" aria-hidden />
              <h3 className="display text-xl">{step.title}</h3>
              {step.text && <p className="mt-3 text-ink-muted">{step.text}</p>}
            </Reveal>
          ))}
        </ol>
      </Section>
    );
  }

  return (
    <Section>
      <SectionTitle title={data.title} />
      <ol className="relative space-y-10 border-l border-line pl-8">
        {steps.map((step, index) => (
          <Reveal as="li" key={index} className="relative">
            <span className="absolute -left-[2.3rem] top-1.5 size-3 rounded-full bg-[var(--accent-ink)] shadow-[0_0_20px_var(--accent)]" aria-hidden />
            <p className="cartel">{step.period}{step.tag && <span className="ml-3 rounded-full border border-line px-2 py-0.5">{step.tag}</span>}</p>
            <h3 className="display mt-2 text-2xl">{step.title}</h3>
            {step.text && <p className="mt-2 max-w-2xl text-ink-muted">{step.text}</p>}
          </Reveal>
        ))}
      </ol>
    </Section>
  );
}

export function FaqBlock({ data, locale }: BlockProps<FaqData>) {
  const t = getDictionary(locale).blocks;
  const items = data.items ?? [];
  if (items.length === 0) return null;
  return (
    <Section>
      <div className="mx-auto grid max-w-6xl gap-10 lg:grid-cols-[1fr_2fr]">
        <SectionTitle title={data.title ?? t.faq} className="mb-0" />
        <div className="divide-y divide-line border-y border-line">
          {items.map((item) => (
            <details key={item.question} className="group py-6">
              <summary className="flex cursor-pointer list-none items-center justify-between gap-6 text-lg font-medium">
                {item.question}
                <Plus className="size-5 shrink-0 text-[var(--accent-ink)] transition duration-300 group-open:rotate-45" aria-hidden />
              </summary>
              <p className="mt-4 whitespace-pre-line text-ink-muted">{item.answer}</p>
            </details>
          ))}
        </div>
      </div>
    </Section>
  );
}
