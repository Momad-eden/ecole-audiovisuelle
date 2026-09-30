"use client";

import { usePathname } from "next/navigation";
import type { DomainKey } from "@/lib/types";
import { DomainChrome, type DomainChromeSite } from "./DomainChrome";

/** DomainChrome pour un layout de route (le chemin courant n'est connu que côté client). */
export function RouteDomainChrome({ domain, site, children }: { domain: DomainKey; site: DomainChromeSite; children: React.ReactNode }) {
  return (
    <DomainChrome domain={domain} site={site} path={usePathname()}>
      {children}
    </DomainChrome>
  );
}
