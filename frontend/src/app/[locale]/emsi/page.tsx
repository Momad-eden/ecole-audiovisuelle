import { CmsPageContent, cmsMetadata } from "@/lib/cms-page";
import type { Locale } from "@/lib/i18n/locales";

type Props = { params: Promise<{ locale: Locale }> };

export const generateMetadata = async ({ params }: Props) => cmsMetadata("emsi", (await params).locale);

export default async function EmsiPage({ params }: Props) {
  return <CmsPageContent slug="emsi" locale={(await params).locale} />;
}
