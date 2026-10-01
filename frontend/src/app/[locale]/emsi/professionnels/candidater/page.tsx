import type { Metadata } from "next";
import { ApplicationPage } from "@/components/ApplicationPage";
import { getDictionary } from "@/lib/i18n";
import type { Locale } from "@/lib/i18n/locales";

type Props = { params: Promise<{ locale: Locale }>; searchParams: Promise<{ formation?: string; campus?: string }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  return getDictionary((await params).locale).meta.professionalApply;
}

export default async function ProfessionalApplyPage({ params, searchParams }: Props) {
  const { formation, campus } = await searchParams;
  return <div style={{ ["--accent" as string]: "var(--color-hmi)" }}><ApplicationPage locale={(await params).locale} audience="professional" formation={formation} campus={campus} /></div>;
}
