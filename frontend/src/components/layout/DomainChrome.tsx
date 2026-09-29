import Link from "next/link";
import { ChevronRight } from "lucide-react";
import { accentVars } from "@/lib/contrast";
import { domainSection } from "@/lib/domains";
import type { DomainKey, Site } from "@/lib/types";
import { siteUrl } from "@/lib/utils";

export type DomainChromeSite = Pick<Site, "menus" | "domains">;

/**
 * Repères d'une page de domaine : couleur du domaine (--accent), fil d'Ariane avec JSON-LD BreadcrumbList
 * et sous-navigation issue du menu. Sans section trouvée dans le menu (accueil…), seule la couleur s'applique.
 */
export function DomainChrome({
  domain,
  site,
  path,
  title,
  children,
}: {
  domain: DomainKey;
  site: DomainChromeSite;
  path: string;
  title?: string;
  children: React.ReactNode;
}) {
  const color = domain === "general" ? undefined : site.domains?.[domain]?.color;
  const section = domain === "general" ? null : domainSection(site.menus.main, path);

  const crumbs = [{ label: "Accueil", url: "/" }];
  if (section) {
    crumbs.push({ label: section.parent.label, url: section.parent.url });
    if (section.current && section.current.url !== section.parent.url) crumbs.push(section.current);
    if (title && crumbs.at(-1)?.url !== path && crumbs.at(-1)?.label !== title) crumbs.push({ label: title, url: path });
  }
  const jsonLd = {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    itemListElement: crumbs.map((crumb, index) => ({ "@type": "ListItem", position: index + 1, name: crumb.label, item: `${siteUrl}${crumb.url}` })),
  };
  const siblings = section?.parent.children ?? [];

  return (
    <div style={accentVars(color)}>
      {section && (
        <div className="mx-auto max-w-7xl px-4 pt-24 sm:px-6 lg:px-8">
          <nav aria-label="Fil d'Ariane">
            <ol className="flex flex-wrap items-center gap-1.5 text-sm text-ink-muted">
              {crumbs.map((crumb, index) => {
                const last = index === crumbs.length - 1;
                return (
                  <li key={crumb.url} className="flex items-center gap-1.5">
                    {index > 0 && <ChevronRight className="size-3.5" aria-hidden />}
                    {last ? <span aria-current="page" className="text-ink">{crumb.label}</span> : <Link href={crumb.url} className="hover:text-ink">{crumb.label}</Link>}
                  </li>
                );
              })}
            </ol>
          </nav>
          {siblings.length > 0 && (
            <nav aria-label={`Rubriques ${section.parent.label}`} className="mt-4">
              <ul className="flex gap-2 overflow-x-auto pb-1">
                {siblings.map((item) => {
                  const current = item.url === section.current?.url;
                  return (
                    <li key={item.url} className="shrink-0">
                      <Link
                        href={item.url}
                        aria-current={current ? "page" : undefined}
                        className={"inline-flex min-h-11 items-center rounded-full border px-4 text-sm transition " + (current ? "border-[var(--accent-ink)] bg-[var(--accent)] font-semibold text-on-accent" : "border-line text-ink/80 hover:text-ink")}
                      >
                        {item.label}
                      </Link>
                    </li>
                  );
                })}
              </ul>
            </nav>
          )}
          <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd).replace(/</g, "\\u003c") }} />
        </div>
      )}
      {children}
    </div>
  );
}
