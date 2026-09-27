import Link from "next/link";
import { ApplicationForm } from "@/components/forms/ApplicationForm";
import { api } from "@/lib/api";

export async function ApplicationPage({ audience, formation }: { audience: "school" | "professional"; formation?: string }) {
  const [offerings, site] = await Promise.all([api.offerings(audience), api.site()]);
  const campuses = site.places.filter((place) => place.kind === "campus");
  const program = formation ? await api.program(formation) : null;
  const preselected = program?.cohorts?.flatMap((c) => c.offerings ?? []).find((o) => offerings.some((open) => open.id === o.id))?.id;

  return (
    <div className="mx-auto max-w-4xl px-4 pb-16 pt-36 sm:px-6">
      <p className="cartel mb-4">{audience === "professional" ? "Espace Professionnels" : "Candidature"}</p>
      <h1 className="font-display text-4xl font-medium sm:text-6xl">{audience === "professional" ? "Candidature professionnelle" : "Candidater à l'EMSI"}</h1>
      {offerings.length === 0 ? (
        <div className="mt-10 rounded-3xl border border-line bg-night-2 p-8">
          <p className="text-lg">Aucune candidature n&apos;est ouverte pour le moment.</p>
          <p className="mt-2 text-ink-muted">
            {audience === "professional" ? "Le recrutement du Volet 2 est prévu en janvier 2027. " : ""}
            Consultez le calendrier des sessions ou écrivez-nous pour être prévenu de l&apos;ouverture.
          </p>
          <div className="mt-6 flex flex-wrap gap-3">
            <Link href={audience === "professional" ? "/professionnels" : "/formations"} className="rounded-full border border-line px-5 py-2">Voir les formations</Link>
            <Link href="/contact" className="rounded-full bg-brand px-5 py-2 font-semibold text-on-accent">Nous contacter</Link>
          </div>
        </div>
      ) : (
        <div className="mt-10"><ApplicationForm offerings={offerings} audience={audience} campuses={campuses} preselected={preselected ? String(preselected) : undefined} /></div>
      )}
    </div>
  );
}
