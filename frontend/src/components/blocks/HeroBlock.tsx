import { ButtonLink } from "@/components/ui/ButtonLink";
import { MediaImage } from "@/components/ui/MediaImage";
import { accentVars } from "@/lib/contrast";
import { cn, frenchSpacing } from "@/lib/utils";
import { HeroVideo } from "./HeroVideo";
import { StageHero } from "./StageHero";
import { ArtHero } from "./ArtHero";
import { ProjectionHero } from "./ProjectionHero";
import { StudioHero } from "./StudioHero";
import { CinemaHero } from "./CinemaHero";
import { CompactHero, EditorialHero, MosaicHero, PosterHero, SpotlightHero, heroTitleSize } from "./HeroLayouts";
import type { HeroData } from "./types";

export function HeroBlock({ data, first }: { data: HeroData; first: boolean }) {
  switch (data.layout) {
    case "stage":
    case "events":
      return <StageHero data={data} first={first} variant={data.layout} />;
    case "masterpiece": return <ArtHero data={data} first={first} />;
    case "projection": return <ProjectionHero data={data} first={first} />;
    case "studio": return <StudioHero data={data} first={first} />;
    case "cinema": return <CinemaHero data={data} first={first} />;
    case "spotlight": return <SpotlightHero data={data} first={first} />;
    case "editorial": return <EditorialHero data={data} first={first} />;
    case "poster": return <PosterHero data={data} first={first} />;
    case "mosaic": return <MosaicHero data={data} first={first} />;
    case "compact": return <CompactHero data={data} first={first} />;
  }
  const split = data.layout === "split" && data.image;
  const Heading = first ? "h1" : "h2";

  return (
    <section
      className={cn("relative isolate overflow-hidden", split ? "py-16 sm:py-24" : "flex min-h-[72vh] items-end pb-16 pt-36 sm:pb-24")}
      style={accentVars(data.accent)}
    >
      {!split && (
        <div className="absolute inset-0 -z-10">
          {data.image && <MediaImage image={data.image} sizes="100vw" priority={first} className="opacity-45" />}
          {data.videoLoop && <HeroVideo src={data.videoLoop} poster={data.image?.url} />}
          <div className="absolute inset-0 bg-gradient-to-t from-night via-night/60 to-night/30" />
        </div>
      )}
      <div className="beam absolute inset-0 -z-10" aria-hidden />

      <div className={cn("mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8", split && "grid items-center gap-12 lg:grid-cols-2")}>
        <div className={cn(split ? "max-w-3xl" : "max-w-5xl")}>
          {data.eyebrow && <p className="cartel mb-5 flex items-center gap-3 text-[var(--accent-ink)]"><span className="h-px w-10 bg-[var(--accent-ink)]" aria-hidden />{data.eyebrow}</p>}
          <Heading className={cn("display text-balance", heroTitleSize(data.title))}>{frenchSpacing(data.title)}</Heading>
          {data.subtitle && <p className="mt-6 max-w-2xl text-lg text-ink/80 sm:text-xl">{data.subtitle}</p>}
          {data.buttons && data.buttons.length > 0 && (
            <div className="mt-10 flex flex-wrap gap-3">
              {data.buttons.map((button) => (
                <ButtonLink key={button.url + button.label} href={button.url} size="lg" variant={button.style === "secondary" ? "secondary" : "primary"}>{button.label}</ButtonLink>
              ))}
            </div>
          )}
        </div>
        {split && (
          <div className="relative aspect-[4/3] overflow-hidden rounded-[2rem] border border-line">
            <MediaImage image={data.image} sizes="(min-width: 1024px) 50vw, 100vw" priority={first} />
          </div>
        )}
      </div>
    </section>
  );
}
