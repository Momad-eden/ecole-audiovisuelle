import type { Metadata } from "next";
import Link from "next/link";
import { MediaImage } from "@/components/ui/MediaImage";
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
              <h3 className="mt-2 font-display text-3xl">{e.title}</h3>
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
      <section className="beam pb-4 pt-24">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <p className="cartel mb-4">Musée numérique</p>
          <h1 className="font-display text-5xl font-medium sm:text-7xl">Expositions</h1>
        </div>
      </section>
      <Section>
        {exhibitions.length === 0 && <p className="text-ink-muted">Aucune exposition pour le moment.</p>}
        {current.length > 0 && (<><SectionTitle title="En cours et à venir" /><ExhibitionList items={current} /></>)}
        {past.length > 0 && (<div className="mt-16"><SectionTitle title="Expositions passées" /><ExhibitionList items={past} /></div>)}
      </Section>
    </>
  );
}
