"use client";

import { usePathname } from "next/navigation";
import { delocalizedPath } from "@/lib/i18n/locales";
import type { DomainKey } from "@/lib/types";
import { DomainChrome, type DomainChromeSite } from "./DomainChrome";

/** DomainChrome pour un layout de route (le chemin courant n'est connu que côté client ; sans /en, comme les adresses du menu). */
export function RouteDomainChrome({ domain, site, children }: { domain: DomainKey; site: DomainChromeSite; children: React.ReactNode }) {
  return (
    <DomainChrome domain={domain} site={site} path={delocalizedPath(usePathname())}>
      {children}
    </DomainChrome>
  );
}
