import { FixedDomainLayout } from "@/lib/domain-layout";
import { asLocale } from "@/lib/i18n/locales";

export default async function Layout({ children, params }: { children: React.ReactNode; params: Promise<{ locale: string }> }) {
  return <FixedDomainLayout domain="emsi" locale={asLocale((await params).locale)}>{children}</FixedDomainLayout>;
}
