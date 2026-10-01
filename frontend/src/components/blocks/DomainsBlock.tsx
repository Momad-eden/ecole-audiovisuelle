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
 * « Nos trois maisons » : trois grands panneaux côte à côte (empilés sous 1024 px), un par domaine,
 * chacun dans sa couleur ; à la souris, le panneau survolé s'élargit (CSS, voir .domains-panel).
 * Chaque panneau est un seul lien, dont le nom accessible est son titre.
 * La phrase d'intention est lue en premier (h1 si le bloc ouvre la page) mais affichée sous les panneaux.
 */
export function DomainsBlock({ data, first, pageTitle, locale }: { data: DomainsData; first: boolean; pageTitle?: string; locale: Locale }) {
  const panels = (data.panels ?? []).slice(0, 3);
  if (panels.length === 0) return null;
  const intro = data.intro?.trim();
  const IntroTag = first ? "h1" : "p";

  return (
    <section
      data-testid="domains-block"
      data-first={first || undefined}
      aria-label={intro ? undefined : getDictionary(locale).domains.label}
      // Ouverture de page : plein écran ; après un film d'ouverture, un peu moins haut pour inviter à poursuivre.
      className={cn("scene-dark relative isolate flex flex-col overflow-hidden bg-night lg:min-h-[40rem]", first ? "lg:h-[100svh]" : "lg:h-[82svh]")}
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
        "domains-panel group relative isolate flex min-h-[26rem] flex-col justify-end overflow-hidden sm:min-h-[32rem] lg:min-h-0 lg:flex-1 lg:basis-0",
        "has-[a:focus-visible]:outline has-[a:focus-visible]:outline-2 has-[a:focus-visible]:-outline-offset-4 has-[a:focus-visible]:outline-[var(--accent)]",
        index > 0 && "border-t border-line lg:border-l lg:border-t-0",
      )}
      style={accentVars(panel.color)}
    >
      <div className="absolute inset-0 -z-20 brightness-[0.62] saturate-[0.85] motion-safe:transition-transform motion-safe:duration-700 group-hover:scale-[1.04]" aria-hidden>
        {panel.image && <MediaImage image={{ ...panel.image, alt: "" }} sizes="(min-width: 1024px) 40vw, 100vw" priority={priority} />}
      </div>
      {/* Teinte du domaine sur la photo, fond presque noir sous le texte : le blanc reste lisible. */}
      <div
        className="absolute inset-0 -z-10"
        style={{ background: "linear-gradient(0deg, var(--color-night) 14%, color-mix(in oklab, var(--accent) 30%, rgb(7 7 10 / 0.72)) 62%, rgb(7 7 10 / 0.6))" }}
        aria-hidden
      />
      {/* Filet de lumière du domaine : il s'allume au survol. */}
      <span className="absolute inset-x-0 bottom-0 h-1 origin-left scale-x-[0.18] bg-[var(--accent)] shadow-[0_0_24px_var(--accent)] motion-safe:transition-transform motion-safe:duration-500 group-hover:scale-x-100" aria-hidden />

      <div className="relative px-6 pb-10 pt-32 sm:px-10 lg:px-9 lg:pb-14 xl:px-12">
        {panel.eyebrow && <p className="cartel mb-4" style={{ color: "var(--accent-ink)" }}>{panel.eyebrow}</p>}
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
