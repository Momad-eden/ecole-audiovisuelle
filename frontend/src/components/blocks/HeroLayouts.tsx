import { LocaleLink } from "@/components/i18n/LocaleLink";
import { Emphasis } from "@/components/ui/Emphasis";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { MediaImage } from "@/components/ui/MediaImage";
import { InView } from "@/components/motion/InView";
import { UniverseVisual } from "@/components/universe/UniverseVisual";
import { accentVars } from "@/lib/contrast";
import { cn } from "@/lib/utils";
import { HeroVideo } from "./HeroVideo";
import { getDictionary } from "@/lib/i18n";
import type { Locale } from "@/lib/i18n/locales";
import type { HeroData } from "./types";

type Props = { data: HeroData; first: boolean };

/** Taille du titre selon sa longueur : un titre long ne s'étale jamais sur six lignes. */
export function heroTitleSize(title: string, scale: "xl" | "lg" = "lg"): string {
  const length = title.length;
  if (scale === "xl") return length <= 24 ? "text-[clamp(3rem,10vw,9rem)]" : length <= 44 ? "text-[clamp(2.6rem,7.5vw,6.8rem)]" : "text-[clamp(2.2rem,5.2vw,4.8rem)]";
  return length <= 28 ? "text-[clamp(2.6rem,7vw,6.4rem)]" : length <= 52 ? "text-[clamp(2.3rem,5.4vw,4.9rem)]" : "text-[clamp(2rem,4.2vw,3.8rem)]";
}

const accentStyle = (data: HeroData) => accentVars(data.accent);


function Eyebrow({ text, className }: { text?: string; className?: string }) {
  if (!text) return null;
  return (
    <p className={cn("cartel mb-6 flex items-center gap-3 text-[var(--accent-ink)]", className)}>
      <span className="h-px w-10 bg-[var(--accent-ink)]" aria-hidden />
      {text}
    </p>
  );
}

function Buttons({ data, className }: { data: HeroData; className?: string }) {
  if (!data.buttons?.length) return null;
  return (
    <div className={cn("mt-10 flex flex-wrap gap-3", className)}>
      {data.buttons.map((button) => (
        <ButtonLink key={button.url + button.label} href={button.url} size="lg" variant={button.style === "secondary" ? "secondary" : "primary"}>{button.label}</ButtonLink>
      ))}
    </div>
  );
}

/** Projecteur : titre centré, baigné par une poursuite qui tombe du cintre. */
export function SpotlightHero({ data, first }: Props) {
  const Heading = first ? "h1" : "h2";
  return (
    <section className="relative isolate flex min-h-[88svh] items-center overflow-hidden pb-20 pt-36 text-center" style={accentStyle(data)}>
      <div className="absolute inset-0 -z-10" aria-hidden>
        {data.image && <MediaImage image={data.image} sizes="100vw" priority={first} className="opacity-25 mix-blend-luminosity" />}
        {data.videoLoop && <HeroVideo src={data.videoLoop} poster={data.image?.url} className="opacity-25 mix-blend-luminosity" />}
        <div className="spotlight-cone absolute left-1/2 top-0 h-full w-[120vmin] -translate-x-1/2" />
        <div className="absolute bottom-[8%] left-1/2 h-24 w-[70vmin] -translate-x-1/2 rounded-[50%] bg-[radial-gradient(closest-side,color-mix(in_oklab,var(--accent)_45%,transparent),transparent)] blur-md" />
        <div className="absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-night to-transparent" />
      </div>
      <div className="mx-auto w-full max-w-5xl px-4 sm:px-6">
        <Eyebrow text={data.eyebrow} className="justify-center" />
        <Heading className={cn("display text-balance", heroTitleSize(data.title))}><Emphasis text={data.title} /></Heading>
        {data.subtitle && <p className="mx-auto mt-7 max-w-2xl text-lg text-ink/80 sm:text-xl">{data.subtitle}</p>}
        <Buttons data={data} className="justify-center" />
      </div>
    </section>
  );
}

/** Éditorial : grand titre à gauche, portrait à droite, comme une couverture de magazine. */
export function EditorialHero({ data, first }: Props) {
  const Heading = first ? "h1" : "h2";
  return (
    <section className="relative isolate overflow-hidden pb-16 pt-32 sm:pt-40" style={accentStyle(data)}>
      <div className="beam absolute inset-0 -z-10" aria-hidden />
      <div className="mx-auto grid max-w-7xl items-end gap-12 px-4 sm:px-6 lg:grid-cols-[1.25fr_1fr] lg:px-8">
        <div className="pb-4">
          <Eyebrow text={data.eyebrow} />
          <Heading className={cn("display text-balance", heroTitleSize(data.title))}><Emphasis text={data.title} /></Heading>
          {data.subtitle && <p className="mt-7 max-w-xl text-lg text-ink/80 sm:text-xl">{data.subtitle}</p>}
          <Buttons data={data} />
        </div>
        <figure className="relative">
          <div className="relative aspect-[4/5] overflow-hidden rounded-[2rem] border border-line bg-night-2" style={{ background: "radial-gradient(80% 70% at 60% 30%, color-mix(in oklab, var(--accent) 22%, transparent), transparent 70%)" }}>
            {data.image ? <MediaImage image={data.image} sizes="(min-width: 1024px) 40vw, 100vw" priority={first} /> : <InView className="absolute inset-8"><UniverseVisual kind="image" /></InView>}
          </div>
          {data.caption && <figcaption className="cartel mt-4 flex items-center gap-3"><span className="h-px w-6 bg-[var(--accent-ink)]" aria-hidden />{data.caption}</figcaption>}
        </figure>
      </div>
    </section>
  );
}

/** Affiche de concert : titre géant en capitales sur aplat de couleur, photo en bichromie. */
export function PosterHero({ data, first, locale }: Props & { locale: Locale }) {
  const Heading = first ? "h1" : "h2";
  return (
    <section data-first={first || undefined} className="poster-hero relative isolate overflow-hidden bg-[var(--accent-ink)] pb-12 pt-32 text-on-accent sm:pt-36" style={accentStyle(data)}>
      {data.image && (
        <div className="absolute inset-0 -z-10 opacity-20 mix-blend-multiply grayscale" aria-hidden>
          <MediaImage image={data.image} sizes="100vw" priority={first} />
        </div>
      )}
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div className="flex flex-wrap items-center justify-between gap-4 border-b border-current/25 pb-5 font-mono text-xs uppercase tracking-[0.2em]">
          <span>{data.eyebrow ?? "EMSI"}</span>
          <span aria-hidden>{getDictionary(locale).hero.posterTagline}</span>
        </div>
        <Heading className={cn("display-condensed mt-8 uppercase leading-[0.86] text-balance", data.title.length <= 30 ? "text-[clamp(3.4rem,13vw,12rem)]" : "text-[clamp(2.8rem,8.5vw,8rem)]")}>
          <Emphasis text={data.title} />
        </Heading>
        <div className="mt-10 grid gap-8 border-t border-current/25 pt-6 lg:grid-cols-[1fr_auto] lg:items-end">
          {data.subtitle && <p className="max-w-2xl text-lg font-medium sm:text-xl">{data.subtitle}</p>}
          {data.buttons?.length ? (
            <div className="flex flex-wrap gap-3">
              {data.buttons.map((button, i) => (
                <LocaleLink key={button.url + button.label} href={button.url} className={cn("inline-flex min-h-14 items-center rounded-full px-7 font-semibold transition", i === 0 ? "bg-current hover:opacity-90" : "border border-current/50 hover:bg-current/10")}>{i === 0 ? <span className="text-[var(--accent-ink)]">{button.label}</span> : button.label}</LocaleLink>
              ))}
            </div>
          ) : null}
        </div>
        {data.caption && <p className="mt-6 text-right font-mono text-xs uppercase tracking-[0.2em] opacity-80">{data.caption}</p>}
      </div>
    </section>
  );
}

/** Mosaïque : titre et collage de trois ou quatre photos. */
export function MosaicHero({ data, first }: Props) {
  const Heading = first ? "h1" : "h2";
  const images = (data.images ?? []).slice(0, 4);
  return (
    <section className="relative isolate overflow-hidden pb-16 pt-32 sm:pt-40" style={accentStyle(data)}>
      <div className="beam absolute inset-0 -z-10" aria-hidden />
      <div className="mx-auto grid max-w-7xl items-center gap-12 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
        <div>
          <Eyebrow text={data.eyebrow} />
          <Heading className={cn("display text-balance", heroTitleSize(data.title))}><Emphasis text={data.title} /></Heading>
          {data.subtitle && <p className="mt-7 max-w-xl text-lg text-ink/80 sm:text-xl">{data.subtitle}</p>}
          <Buttons data={data} />
        </div>
        {images.length > 0 ? (
          <figure>
            <div className="grid h-[min(70vh,560px)] grid-cols-2 grid-rows-2 gap-3">
              {images.map((image, index) => (
                <div key={`${index}-${image.url}`} className={cn("relative overflow-hidden rounded-3xl border border-line", index === 0 && "row-span-2", images.length === 2 && index === 1 && "row-span-2")}>
                  <MediaImage image={image} sizes="(min-width: 1024px) 25vw, 50vw" priority={first && index === 0} className="transition duration-700 hover:scale-105" />
                </div>
              ))}
            </div>
            {data.caption && <figcaption className="cartel mt-4">{data.caption}</figcaption>}
          </figure>
        ) : (
          <div className="relative aspect-[4/3] overflow-hidden rounded-[2rem] border border-line bg-night-2"><InView className="absolute inset-8"><UniverseVisual kind="stage" /></InView></div>
        )}
      </div>
    </section>
  );
}

/** Sobre : en-tête court pour les pages secondaires (mentions légales, pages libres). */
export function CompactHero({ data, first }: Props) {
  const Heading = first ? "h1" : "h2";
  return (
    <section className="relative isolate overflow-hidden pb-10 pt-32 sm:pt-36" style={accentStyle(data)}>
      <div className="beam absolute inset-0 -z-10 opacity-60" aria-hidden />
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <Eyebrow text={data.eyebrow} />
        <Heading className="display text-[clamp(2.1rem,4.6vw,3.8rem)] text-balance"><Emphasis text={data.title} /></Heading>
        {data.subtitle && <p className="mt-5 max-w-2xl text-lg text-ink/80">{data.subtitle}</p>}
        <Buttons data={data} className="mt-8" />
      </div>
    </section>
  );
}
