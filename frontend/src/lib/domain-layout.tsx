import { RouteDomainChrome } from "@/components/layout/RouteDomainChrome";
import { api } from "@/lib/api";
import type { Locale } from "@/lib/i18n/locales";
import type { DomainKey } from "@/lib/types";

/** Enveloppe d'un layout de route dont le domaine est fixé en dur (ex. tout /emsi). */
export async function FixedDomainLayout({ domain, locale, children }: { domain: DomainKey; locale: Locale; children: React.ReactNode }) {
  const { menus, domains } = await api.site(locale);
  return (
    <RouteDomainChrome domain={domain} site={{ menus, domains }}>
      {children}
    </RouteDomainChrome>
  );
}
