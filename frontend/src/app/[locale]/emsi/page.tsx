import { CmsPageContent, cmsMetadata } from "@/lib/cms-page";
import type { Metadata } from "next";
import { localeAlternates } from "@/lib/i18n/alternates";
import { asLocale, type Locale } from "@/lib/i18n/locales";

type Props = { params: Promise<{ locale: Locale }> };

const baseMetadata = async ({ params }: Props) => cmsMetadata("emsi", (await params).locale);

export async function generateMetadata(props: Props): Promise<Metadata> {
  const p = await props.params;
  return { ...(await baseMetadata(props)), alternates: localeAlternates("/emsi", asLocale(p.locale)) };
}

export default async function EmsiPage({ params }: Props) {
  return <CmsPageContent slug="emsi" locale={(await params).locale} />;
}
