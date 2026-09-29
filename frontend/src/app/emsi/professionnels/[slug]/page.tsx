import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { ProgramDetail } from "@/components/ProgramDetail";
import { api } from "@/lib/api";

type Props = { params: Promise<{ slug: string }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const program = await api.program((await params).slug);
  return program ? { title: program.seo?.title || program.title, description: program.seo?.description || program.summary || undefined } : {};
}

export default async function ProfessionalProgramPage({ params }: Props) {
  const program = await api.program((await params).slug);
  if (!program || program.audience !== "professional") notFound();

  return (
    <div style={{ ["--accent" as string]: "var(--color-hmi)" }}>
      <ProgramDetail program={program} applyHref={`/emsi/professionnels/candidater?formation=${program.slug}`} />
    </div>
  );
}
