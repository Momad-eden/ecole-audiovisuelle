import { ButtonLink } from "@/components/ui/ButtonLink";
import { MediaImage } from "@/components/ui/MediaImage";
import { cn } from "@/lib/utils";
import { HeroVideo } from "./HeroVideo";
import { StageHero } from "./StageHero";
import type { HeroData } from "./types";

export function HeroBlock({ data, first }: { data: HeroData; first: boolean }) {
  if (data.layout === "stage") return <StageHero data={data} first={first} />;
  const split = data.layout === "split" && data.image;
  const Heading = first ? "h1" : "h2";

  return (
    <section
      className={cn("relative isolate overflow-hidden", split ? "py-16 sm:py-24" : "flex min-h-[72vh] items-end pb-16 pt-36 sm:pb-24")}
      style={data.accent ? { ["--accent" as string]: data.accent } : undefined}
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
        <div className="max-w-3xl">
          {data.eyebrow && <p className="cartel mb-5 flex items-center gap-3 text-[var(--accent-ink)]"><span className="h-px w-10 bg-[var(--accent-ink)]" aria-hidden />{data.eyebrow}</p>}
          <Heading className="display text-[clamp(2.4rem,6.5vw,5.8rem)] text-balance">{data.title}</Heading>
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
