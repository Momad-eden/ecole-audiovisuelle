import { ApplicationForm } from "@/components/forms/ApplicationForm";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { PageHeader } from "@/components/ui/PageHeader";
import { api } from "@/lib/api";
import { resolvePreselection } from "@/lib/application";

type Props = { audience: "school" | "professional"; formation?: string; campus?: string };

export async function ApplicationPage({ audience, formation, campus }: Props) {
  const [offerings, site] = await Promise.all([api.offerings(audience), api.site()]);
  const campuses = site.places.filter((place) => place.kind === "campus");
  // La formation demandée n'est qu'un indice : toute erreur revient à « pas de présélection ».
  const program = formation ? await api.program(formation).catch(() => null) : null;
  const programOfferingIds = (program?.cohorts?.flatMap((c) => c.offerings ?? []) ?? []).map((o) => o.id);
  const { campusId, offeringId, notice } = resolvePreselection({ offerings, campuses, campusSlug: campus, programOfferingIds });
  const professional = audience === "professional";
  const whatsapp = site.settings.whatsapp?.replace(/[^0-9]/g, "");

  return (
    <>
      <PageHeader
        eyebrow={professional ? "Espace Professionnels" : "Candidature"}
        title={professional ? "Candidature professionnelle" : "Candidater à l'EMSI"}
        text={offerings.length > 0 ? "Quelques minutes suffisent. Votre saisie est gardée sur cet appareil si vous devez vous interrompre." : null}
        accent={professional ? "var(--color-hmi)" : undefined}
      />
      <div className="mx-auto max-w-4xl px-4 pb-24 sm:px-6">
        {offerings.length === 0 ? (
          <div className="rounded-[2rem] border border-line bg-night-2 p-8 sm:p-12">
            <p className="display text-2xl sm:text-3xl">Aucune candidature n&apos;est ouverte pour le moment.</p>
            <p className="mt-4 max-w-2xl text-ink-muted">
              {professional ? "Le recrutement du Volet 2 est prévu en janvier 2027. " : "Les prochaines sessions seront annoncées ici. "}
              Écrivez-nous : nous vous préviendrons dès l&apos;ouverture{campuses.length > 1 ? ", à Dakar comme à Saint-Louis" : ""}.
            </p>
            <div className="mt-8 flex flex-wrap gap-3">
              {whatsapp && <ButtonLink href={`https://wa.me/${whatsapp}`}>Nous écrire sur WhatsApp</ButtonLink>}
              <ButtonLink href="/contact" variant={whatsapp ? "secondary" : "primary"}>Nous contacter</ButtonLink>
              <ButtonLink href={professional ? "/emsi/professionnels" : "/emsi/formations"} variant="secondary">Voir les formations</ButtonLink>
            </div>
          </div>
        ) : (
          <ApplicationForm offerings={offerings} audience={audience} campuses={campuses} preselected={offeringId} preselectedCampus={campusId} notice={notice} wanted={programOfferingIds} />
        )}
      </div>
    </>
  );
}
