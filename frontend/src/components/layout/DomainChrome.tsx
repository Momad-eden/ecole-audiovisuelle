import { accentVars } from "@/lib/contrast";
import { domainSection } from "@/lib/domains";
import type { DomainKey, Site } from "@/lib/types";
import { CrumbProvider } from "./domain-crumb";
import { DomainRow } from "./DomainRow";

export type DomainChromeSite = Pick<Site, "menus" | "domains">;

/**
 * Repères d'une page de domaine : couleur du domaine (--accent) et seconde rangée de l'en-tête fixe
 * (fil d'Ariane + sous-navigation issue du menu). Sans section dans le menu (accueil…), seule la couleur s'applique.
 * Les pages de détail terminent le fil d'Ariane avec <CurrentCrumb title=… />.
 */
export function DomainChrome({ domain, site, path, children }: { domain: DomainKey; site: DomainChromeSite; path: string; title?: string; children: React.ReactNode }) {
  const color = domain === "general" ? undefined : site.domains?.[domain]?.color;
  const section = domain === "general" ? null : domainSection(site.menus.main, path);

  return (
    <div style={accentVars(color)}>
      <CrumbProvider>
        {section && <DomainRow section={section} path={path} />}
        {children}
      </CrumbProvider>
    </div>
  );
}
