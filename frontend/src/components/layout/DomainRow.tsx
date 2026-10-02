"use client";

import { LocaleLink } from "@/components/i18n/LocaleLink";
import { useEffect, useState } from "react";
import { ChevronRight } from "lucide-react";
import type { DomainSection } from "@/lib/domains";
import { cn } from "@/lib/utils";
import { siteUrl } from "@/lib/utils";
import { useLocale, useT } from "@/components/i18n/LocaleProvider";
import { localizedPath } from "@/lib/i18n/locales";
import { useCrumbTitle } from "./domain-crumb";

/**
 * Seconde rangée de l'en-tête fixe : fil d'Ariane (à gauche) et sous-navigation du domaine (à droite).
 * Même comportement que l'en-tête : transparente sur le héros, fond dès qu'on défile.
 */
export function DomainRow({ section, path }: { section: DomainSection; path: string }) {
  const title = useCrumbTitle();
  const locale = useLocale();
  const t = useT().breadcrumbs;
  const [scrolled, setScrolled] = useState(false);

  useEffect(() => {
    const update = () => setScrolled(window.scrollY > 24);
    update();
    window.addEventListener("scroll", update, { passive: true });
    return () => window.removeEventListener("scroll", update);
  }, []);

  const clean = path.length > 1 ? path.replace(/\/+$/, "") : path;
  const crumbs: { label: string; url: string }[] = [{ label: t.home, url: "/" }, { label: section.parent.label, url: section.parent.url }];
  if (section.current && section.current.url !== section.parent.url) crumbs.push(section.current);
  if (title && clean !== crumbs.at(-1)!.url) crumbs.push({ label: title, url: clean });
  // Sans titre de page, le dernier repère n'est « la page courante » que si le chemin est bien le sien.
  const atCurrent = crumbs.at(-1)!.url === clean;
  const jsonLd = atCurrent
    ? {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        itemListElement: crumbs.map((crumb, index) => ({ "@type": "ListItem", position: index + 1, name: crumb.label, item: `${siteUrl}${localizedPath(crumb.url, locale)}` })),
      }
    : null;
  const siblings = section.parent.children ?? [];

  return (
    <div data-domain-row data-scrolled={scrolled ? "" : undefined} className="domain-row fixed inset-x-0 top-18 z-40 transition-transform duration-500 ease-out motion-reduce:transition-none">
      <div className="domain-row-bar mx-auto flex h-11 max-w-7xl items-center justify-between gap-4 px-5 sm:px-6 lg:px-8">
        <nav aria-label={t.label} className="hidden min-w-0 sm:block">
          <ol className="flex items-center gap-1.5 whitespace-nowrap text-xs text-ink-muted">
            {crumbs.map((crumb, index) => {
              const last = index === crumbs.length - 1;
              return (
                <li key={crumb.url + crumb.label} className="flex min-w-0 items-center gap-1.5">
                  {index > 0 && <ChevronRight className="size-3 shrink-0" aria-hidden />}
                  {last && atCurrent ? <span aria-current="page" className="truncate text-ink">{crumb.label}</span> : <LocaleLink href={crumb.url} className="hover:text-ink">{crumb.label}</LocaleLink>}
                </li>
              );
            })}
          </ol>
        </nav>
        {siblings.length > 0 && (
          <nav aria-label={t.sections(section.parent.label)} className="min-w-0 max-sm:w-full">
            {/* Sur téléphone, la liste défile : un fondu à droite l'indique. */}
            <ul className="flex items-center gap-1 overflow-x-auto pointer-coarse:gap-2 [scrollbar-width:none] max-sm:pr-6 max-sm:[mask-image:linear-gradient(90deg,#000_85%,transparent)]">
              {siblings.map((item) => {
                const here = item.url === section.current?.url;
                return (
                  <li key={item.url + item.label} className="shrink-0">
                    <LocaleLink
                      href={item.url}
                      aria-current={here ? (item.url === clean ? "page" : "location") : undefined}
                      // Zone touchable de 44 px (ui-ux-pro-max : touch-target-size) pour une pastille visible de 32 px.
                      className={cn("relative inline-flex min-h-8 items-center rounded-full px-3 text-xs transition after:absolute after:-inset-y-1.5 after:inset-x-0 after:content-[''] pointer-coarse:text-sm", here ? "bg-[var(--accent)] font-semibold text-on-accent" : "text-ink/80 hover:text-ink")}
                    >
                      {item.label}
                    </LocaleLink>
                  </li>
                );
              })}
            </ul>
          </nav>
        )}
        {jsonLd && <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd).replace(/</g, "\\u003c") }} />}
      </div>
    </div>
  );
}
