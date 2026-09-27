import type { Metadata } from "next";
import { notFound, permanentRedirect } from "next/navigation";
import { ProgramDetail } from "@/components/ProgramDetail";
import { api } from "@/lib/api";

type Props = { params: Promise<{ slug: string }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const program = await api.program((await params).slug);
  return program ? { title: program.seo?.title || program.title, description: program.seo?.description || program.summary || undefined } : {};
}

export default async function ProgramPage({ params }: Props) {
  const { slug } = await params;
  const program = await api.program(slug);
  if (!program) notFound();
  if (program.audience === "professional") permanentRedirect(`/professionnels/${program.slug}`);

  return <ProgramDetail program={program} applyHref={`/candidater?formation=${program.slug}`} />;
}
