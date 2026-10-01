import { CmsPageContent, cmsMetadata } from "@/lib/cms-page";
import type { Metadata } from "next";
import { localeAlternates } from "@/lib/i18n/alternates";
import { asLocale, type Locale } from "@/lib/i18n/locales";

type Props = { params: Promise<{ locale: Locale; slug: string[] }> };

/** Pages à blocs de l'école (/emsi/dakar, /emsi/saint-louis…), gérées dans l'administration. */
async function baseMetadata({ params }: Props) {
  const { locale, slug } = await params;
  return cmsMetadata(`emsi/${slug.join("/")}`, locale);
}

export async function generateMetadata(props: Props): Promise<Metadata> {
  const p = await props.params;
  return { ...(await baseMetadata(props)), alternates: localeAlternates(`/emsi/${p.slug.join("/")}`, asLocale(p.locale)) };
}

export default async function EmsiCmsPage({ params }: Props) {
  const { locale, slug } = await params;
  return <CmsPageContent slug={`emsi/${slug.join("/")}`} locale={locale} />;
}
