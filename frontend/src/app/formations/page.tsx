import type { Metadata } from "next";
import { ProgramCard } from "@/components/ProgramCard";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { Section } from "@/components/ui/Section";
import { api } from "@/lib/api";

export const metadata: Metadata = { title: "Formations", description: "Les formations de l'EMSI aux métiers du son, de la lumière, de l'image et du spectacle vivant." };

export default async function ProgramsPage() {
  const programs = await api.programs("school");

  return (
    <>
      <section className="beam pb-4 pt-24">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <p className="cartel mb-4">Formations</p>
          <h1 className="max-w-4xl font-display text-5xl font-medium text-balance sm:text-7xl">Apprendre les métiers du son et de l&apos;image</h1>
        </div>
      </section>
      <Section>
        {programs.length > 0 ? (
          <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            {programs.map((program) => <ProgramCard key={program.id} program={program} />)}
          </div>
        ) : (
          <div className="rounded-3xl border border-dashed border-line p-10 text-center">
            <p className="text-lg text-ink-muted">Le catalogue des formations sera publié prochainement.</p>
            <div className="mt-6 flex justify-center gap-3">
              <ButtonLink href="/contact" variant="secondary">Nous contacter</ButtonLink>
              <ButtonLink href="/professionnels">Espace Professionnels</ButtonLink>
            </div>
          </div>
        )}
      </Section>
    </>
  );
}
