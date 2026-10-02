import type { Metadata } from "next";
import { ApplicationPage } from "@/components/ApplicationPage";
import { getDictionary } from "@/lib/i18n";
import type { Locale } from "@/lib/i18n/locales";
import { localeAlternates } from "@/lib/i18n/alternates";
import { asLocale } from "@/lib/i18n/locales";

type Props = { params: Promise<{ locale: Locale }>; searchParams: Promise<{ formation?: string; campus?: string }> };

async function baseMetadata({ params }: Props): Promise<Metadata> {
  return getDictionary((await params).locale).meta.professionalApply;
}

export async function generateMetadata(props: Props): Promise<Metadata> {
  const p = await props.params;
  return { ...(await baseMetadata(props)), alternates: localeAlternates("/emsi/professionnels/candidater", asLocale(p.locale)) };
}

export default async function ProfessionalApplyPage({ params, searchParams }: Props) {
  const { formation, campus } = await searchParams;
  return <div style={{ ["--accent" as string]: "var(--color-hmi)" }}><ApplicationPage locale={(await params).locale} audience="professional" formation={formation} campus={campus} /></div>;
}
