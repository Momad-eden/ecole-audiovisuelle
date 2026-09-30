import { CmsPageContent, cmsMetadata } from "@/lib/cms-page";
import type { Locale } from "@/lib/i18n/locales";

type Props = { params: Promise<{ locale: Locale; slug: string[] }> };

/** Pages à blocs de l'école (/emsi/dakar, /emsi/saint-louis…), gérées dans l'administration. */
export async function generateMetadata({ params }: Props) {
  const { locale, slug } = await params;
  return cmsMetadata(`emsi/${slug.join("/")}`, locale);
}

export default async function EmsiCmsPage({ params }: Props) {
  const { locale, slug } = await params;
  return <CmsPageContent slug={`emsi/${slug.join("/")}`} locale={locale} />;
}
