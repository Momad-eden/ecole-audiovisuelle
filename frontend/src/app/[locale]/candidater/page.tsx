import type { Metadata } from "next";
import { ApplicationPage } from "@/components/ApplicationPage";
import { getDictionary } from "@/lib/i18n";
import type { Locale } from "@/lib/i18n/locales";

type Props = { params: Promise<{ locale: Locale }>; searchParams: Promise<{ formation?: string; campus?: string }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  return getDictionary((await params).locale).meta.apply;
}

export default async function ApplyPage({ params, searchParams }: Props) {
  const { formation, campus } = await searchParams;
  return <ApplicationPage locale={(await params).locale} audience="school" formation={formation} campus={campus} />;
}
