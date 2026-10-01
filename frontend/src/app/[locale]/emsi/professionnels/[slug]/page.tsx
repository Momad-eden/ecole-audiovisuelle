import { CurrentCrumb } from "@/components/layout/domain-crumb";
import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { ProgramDetail } from "@/components/ProgramDetail";
import { api } from "@/lib/api";
import type { Locale } from "@/lib/i18n/locales";

type Props = { params: Promise<{ locale: Locale; slug: string }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { locale, slug } = await params;
  const program = await api.program(slug, locale);
  return program ? { title: program.seo?.title || program.title, description: program.seo?.description || program.summary || undefined } : {};
}

export default async function ProfessionalProgramPage({ params }: Props) {
  const { locale, slug } = await params;
  const program = await api.program(slug, locale);
  if (!program || program.audience !== "professional") notFound();

  return (
    <div style={{ ["--accent" as string]: "var(--color-hmi)" }}>
      <CurrentCrumb title={program.title} />
      <ProgramDetail program={program} applyHref={`/emsi/professionnels/candidater?formation=${program.slug}`} locale={locale} />
    </div>
  );
}
