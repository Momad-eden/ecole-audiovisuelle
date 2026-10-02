import { LocaleLink } from "@/components/i18n/LocaleLink";
import { Emphasis } from "@/components/ui/Emphasis";
import { ArrowRight } from "lucide-react";
import { MediaImage } from "@/components/ui/MediaImage";
import { accentVars } from "@/lib/contrast";
import { cn, frenchSpacing, isExternal } from "@/lib/utils";
import { getDictionary } from "@/lib/i18n";
import type { Locale } from "@/lib/i18n/locales";
import type { DomainPanel, DomainsData } from "./types";

/**
 * « Nos trois lieux » : trois grands panneaux côte à côte (empilés sous 1024 px), un par domaine,
 * chacun dans sa couleur ; à la souris, le panneau survolé s'élargit (CSS, voir .domains-panel).
 * Chaque panneau est un seul lien, dont le nom accessible est son titre.
 * Bloc d'ouverture : la phrase d'intention est le h1, lue en premier mais affichée sous les panneaux.
 * Après un autre bloc (film d'ouverture) : elle devient le titre de la section, au-dessus des panneaux.
 */
export function DomainsBlock({ data, first, pageTitle, locale }: { data: DomainsData; first: boolean; pageTitle?: string; locale: Locale }) {
  const panels = (data.panels ?? []).slice(0, 3);
  if (panels.length === 0) return null;
  const intro = data.intro?.trim();
  const IntroTag = first ? "h1" : "p";
  const dictionary = getDictionary(locale).domains;

  if (!first) {
    // Après le film d'ouverture : trois cartes encadrées, avec marges, dans une section qui suit le thème
    // (claire ou sombre) ; seules les cartes restent « de nuit » pour que le texte reste lisible sur la photo.
    return (
      <section data-testid="domains-block" className="relative isolate py-20 sm:py-28">
        <div className="mx-auto mb-10 max-w-7xl px-4 sm:mb-14 sm:px-6 lg:px-8">
          <p className="cartel mb-4 flex items-center gap-3 text-[var(--accent-ink)]"><span className="h-px w-10 bg-[var(--accent-ink)]" aria-hidden />{dictionary.label}</p>
          {intro && <h2 className="display max-w-3xl text-balance text-[clamp(2.2rem,4.4vw,3.6rem)]"><Emphasis text={intro} /></h2>}
          {!intro && <h2 className="sr-only">{dictionary.label}</h2>}
        </div>
        <ul className="mx-auto grid max-w-7xl gap-4 px-5 sm:px-6 lg:grid-cols-3 lg:gap-5 lg:px-8">
          {panels.map((panel, index) => <li key={`${panel.domain}-${index}`}><DomainCard panel={panel} index={index} /></li>)}
        </ul>
      </section>
    );
  }

  return (
    <section
      data-testid="domains-block"
      data-first={first || undefined}
      aria-label={intro ? undefined : getDictionary(locale).domains.label}
      className="scene-dark relative isolate flex flex-col overflow-hidden bg-night lg:h-[100svh] lg:min-h-[40rem]"
    >
      {intro ? (
        <div className="order-last border-t border-line bg-night px-6 py-8 sm:px-10 lg:py-10">
          <IntroTag className="display mx-auto max-w-7xl text-[clamp(1.6rem,3.4vw,3rem)] uppercase text-balance"><Emphasis text={intro} /></IntroTag>
        </div>
      ) : (
        first && pageTitle && <h1 className="sr-only">{pageTitle}</h1>
      )}
      <div className="grid lg:flex lg:min-h-0 lg:flex-1">
        {panels.map((panel, index) => <Panel key={`${panel.domain}-${index}`} panel={panel} index={index} priority={first} />)}
      </div>
    </section>
  );
}

function Panel({ panel, index, priority }: { panel: DomainPanel; index: number; priority: boolean }) {
  const external = isExternal(panel.url);
  const linkClass = "after:absolute after:inset-0 after:z-10 after:content-[''] focus-visible:outline-none";
  const title = frenchSpacing(panel.title);

  return (
    <article
      data-testid="domain-panel"
      className={cn(
        "domains-panel group relative isolate flex min-h-[23rem] flex-col justify-end overflow-hidden sm:min-h-[28rem] lg:min-h-0 lg:flex-1 lg:basis-0",
        "has-[a:focus-visible]:outline has-[a:focus-visible]:outline-2 has-[a:focus-visible]:-outline-offset-4 has-[a:focus-visible]:outline-[var(--accent)]",
        index > 0 && "border-t border-line lg:border-l lg:border-t-0",
      )}
      style={accentVars(panel.color)}
    >
      <div className="absolute inset-0 -z-20 brightness-[0.72] saturate-[0.9] motion-safe:transition-transform motion-safe:duration-700 group-hover:scale-[1.04]" aria-hidden>
        {panel.image && <MediaImage image={{ ...panel.image, alt: "" }} sizes="(min-width: 1024px) 40vw, 100vw" priority={priority} />}
      </div>
      {/* Teinte du domaine sur la photo, fond presque noir sous le texte : le blanc reste lisible. */}
      <div
        className="absolute inset-0 -z-10"
        style={{ background: "linear-gradient(0deg, var(--color-night) 8%, color-mix(in oklab, var(--accent) 26%, rgb(7 7 10 / 0.62)) 55%, rgb(7 7 10 / 0.25))" }}
        aria-hidden
      />
      {/* Filet de lumière du domaine : il s'allume au survol. */}
      <span className="absolute inset-x-0 bottom-0 h-1 origin-left scale-x-[0.18] bg-[var(--accent)] shadow-[0_0_24px_var(--accent)] motion-safe:transition-transform motion-safe:duration-500 group-hover:scale-x-100" aria-hidden />

      <div className="relative px-6 pb-10 pt-24 sm:px-10 lg:px-9 lg:pb-14 xl:px-12">
        <p className="cartel mb-4 flex items-center gap-3" style={{ color: "var(--accent-ink)" }}>
          <span className="tabular-nums text-ink/70">{String(index + 1).padStart(2, "0")}</span>
          <span className="h-px w-6 bg-[var(--accent-ink)]" aria-hidden />
          {panel.eyebrow}
        </p>
        <h2 className="display text-[clamp(2.4rem,3.6vw,4.4rem)] uppercase text-balance text-ink">
          {external ? (
            <a href={panel.url} target="_blank" rel="noopener noreferrer" className={linkClass}>{title}</a>
          ) : (
            <LocaleLink href={panel.url} className={linkClass}>{title}</LocaleLink>
          )}
        </h2>
        {panel.text && <p className="mt-5 max-w-sm text-base text-ink/85 sm:text-lg">{frenchSpacing(panel.text)}</p>}
        {panel.label && (
          <span className="mt-7 inline-flex items-center gap-2 text-sm font-semibold text-[var(--accent-ink)]" aria-hidden>
            {panel.label}
            <ArrowRight className="size-4 motion-safe:transition-transform motion-safe:duration-300 group-hover:translate-x-1" />
          </span>
        )}
      </div>
    </article>
  );
}

/** Carte d'un domaine (section après le film) : photo pleine carte, teinte du domaine, filet lumineux au survol. */
function DomainCard({ panel, index }: { panel: DomainPanel; index: number }) {
  const external = isExternal(panel.url);
  const linkClass = "after:absolute after:inset-0 after:z-10 after:content-[''] focus-visible:outline-none";
  const title = frenchSpacing(panel.title);

  return (
    <article
      data-testid="domain-panel"
      className={cn(
        "scene-dark group relative isolate flex h-[25rem] flex-col justify-end overflow-hidden rounded-[2rem] border border-line bg-night shadow-[0_24px_60px_-30px_rgb(0_0_0/0.6)]",
        "motion-safe:transition motion-safe:duration-500 hover:-translate-y-1 hover:shadow-[0_32px_70px_-28px_color-mix(in_oklab,var(--accent)_45%,transparent)] sm:h-[30rem] lg:h-[36rem]",
        "has-[a:focus-visible]:outline has-[a:focus-visible]:outline-2 has-[a:focus-visible]:outline-offset-2 has-[a:focus-visible]:outline-[var(--accent)]",
      )}
      style={accentVars(panel.color)}
    >
      <div className="absolute inset-0 -z-20 brightness-[0.78] motion-safe:transition-transform motion-safe:duration-700 group-hover:scale-[1.06]" aria-hidden>
        {panel.image && <MediaImage image={{ ...panel.image, alt: "" }} sizes="(min-width: 1024px) 33vw, 100vw" />}
      </div>
      <div
        className="absolute inset-0 -z-10"
        style={{ background: "linear-gradient(0deg, rgb(7 7 10 / 0.95) 10%, color-mix(in oklab, var(--accent) 22%, rgb(7 7 10 / 0.55)) 55%, rgb(7 7 10 / 0.15))" }}
        aria-hidden
      />
      <span className="absolute left-6 right-6 top-6 flex items-center justify-between sm:left-8 sm:right-8 sm:top-8" aria-hidden>
        <span className="cartel tabular-nums text-white/80">{String(index + 1).padStart(2, "0")}</span>
        <span className="grid size-11 place-items-center rounded-full border border-white/25 bg-black/20 text-white backdrop-blur transition group-hover:border-[var(--accent)] group-hover:bg-[var(--accent)] group-hover:text-on-accent">
          <ArrowRight className="size-4 -rotate-45 transition-transform group-hover:rotate-0" />
        </span>
      </span>
      <div className="relative p-6 sm:p-8">
        {panel.eyebrow && <p className="cartel mb-3" style={{ color: "var(--accent-ink)" }}>{panel.eyebrow}</p>}
        <h3 className="display text-[clamp(2rem,3vw,2.8rem)] uppercase text-balance text-ink">
          {external ? (
            <a href={panel.url} target="_blank" rel="noopener noreferrer" className={linkClass}>{title}</a>
          ) : (
            <LocaleLink href={panel.url} className={linkClass}>{title}</LocaleLink>
          )}
        </h3>
        {panel.text && <p className="mt-3 max-w-sm text-ink/85">{frenchSpacing(panel.text)}</p>}
        {panel.label && (
          <span className="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-[var(--accent-ink)]" aria-hidden>
            {panel.label}
            <ArrowRight className="size-4 motion-safe:transition-transform motion-safe:duration-300 group-hover:translate-x-1" />
          </span>
        )}
      </div>
    </article>
  );
}
