import type { Metadata } from "next";
import Link from "next/link";
import { MediaImage } from "@/components/ui/MediaImage";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { EmptyState } from "@/components/ui/EmptyState";
import { PageHeader } from "@/components/ui/PageHeader";
import { Section, SectionTitle } from "@/components/ui/Section";
import { api } from "@/lib/api";
import { formatDate } from "@/lib/utils";
import type { Exhibition } from "@/lib/types";

export const metadata: Metadata = { title: "Expositions", description: "Les expositions temporaires du musée numérique de l'EMSI." };

function ExhibitionList({ items }: { items: Exhibition[] }) {
  return (
    <ul className="grid gap-6 md:grid-cols-2">
      {items.map((e) => (
        <li key={e.id}>
          <Link href={`/expositions/${e.slug}`} className="group relative flex min-h-64 flex-col justify-end overflow-hidden rounded-3xl border border-line p-8">
            <MediaImage image={e.cover} sizes="(min-width: 768px) 50vw, 100vw" className="opacity-40 transition duration-700 group-hover:scale-105" />
            <div className="absolute inset-0 bg-gradient-to-t from-night to-transparent" />
            <div className="relative">
              <p className="cartel">{[formatDate(e.startsOn), formatDate(e.endsOn)].filter(Boolean).join(" – ")}</p>
              <h3 className="display mt-2 text-3xl">{e.title}</h3>
              {e.venue && <p className="mt-1 text-ink/75">{e.venue}</p>}
            </div>
          </Link>
        </li>
      ))}
    </ul>
  );
}

export default async function ExhibitionsPage() {
  const exhibitions = await api.exhibitions();
  const current = exhibitions.filter((e) => e.state !== "past");
  const past = exhibitions.filter((e) => e.state === "past");

  return (
    <>
      <PageHeader eyebrow="Réalisations" title="Expositions" text="Festivals, fins de promotion, projets collectifs : les réalisations des étudiants rassemblées autour d'un thème." />
      <Section className="pt-0 sm:pt-0">
        {exhibitions.length === 0 && (
          <EmptyState title="La prochaine exposition se prépare." text="En attendant, découvrez les réalisations des étudiants, univers par univers." visual="design">
            <ButtonLink href="/realisations">Voir les réalisations</ButtonLink>
          </EmptyState>
        )}
        {current.length > 0 && (<><SectionTitle title="En cours et à venir" /><ExhibitionList items={current} /></>)}
        {past.length > 0 && (<div className="mt-16"><SectionTitle title="Expositions passées" /><ExhibitionList items={past} /></div>)}
      </Section>
    </>
  );
}
