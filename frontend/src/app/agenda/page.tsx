import type { Metadata } from "next";
import { AgendaCard } from "@/components/blocks/ImpactBlocks";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { PageHeader } from "@/components/ui/PageHeader";
import { Section } from "@/components/ui/Section";
import { api } from "@/lib/api";

export const metadata: Metadata = {
  title: "Agenda",
  description: "Concerts, spectacles et événements de l'Espace Habib Faye, d'Impact Live et de l'EMSI, à Saint-Louis et Dakar.",
};

export default async function AgendaPage() {
  const events = await api.agenda();

  return (
    <>
      <PageHeader eyebrow="Agenda" title="Les prochains rendez-vous" text="Concerts, spectacles, festivals et portes ouvertes : tout ce qui se prépare à l'Espace Habib Faye, avec Impact Live et à l'EMSI." />
      <Section className="pt-0 sm:pt-0">
        {events.length > 0 ? (
          <ul className="grid gap-4 md:grid-cols-2">
            {events.map((event) => <li key={event.id}><AgendaCard event={event} /></li>)}
          </ul>
        ) : (
          <div className="rounded-3xl border border-dashed border-line p-10 text-center">
            <p className="text-lg text-ink-muted">La programmation arrive bientôt. Vous organisez un événement ?</p>
            <div className="mt-6 flex justify-center"><ButtonLink href="/events">Découvrir Impact Live Events</ButtonLink></div>
          </div>
        )}
      </Section>
    </>
  );
}
