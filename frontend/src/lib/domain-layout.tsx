import { RouteDomainChrome } from "@/components/layout/RouteDomainChrome";
import { api } from "@/lib/api";
import type { DomainKey } from "@/lib/types";

/** Enveloppe d'un layout de route dont le domaine est fixé en dur (ex. tout /emsi). */
export async function FixedDomainLayout({ domain, children }: { domain: DomainKey; children: React.ReactNode }) {
  const { menus, domains } = await api.site();
  return (
    <RouteDomainChrome domain={domain} site={{ menus, domains }}>
      {children}
    </RouteDomainChrome>
  );
}
