import { ApplicationForm } from "@/components/forms/ApplicationForm";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { PageHeader } from "@/components/ui/PageHeader";
import { api } from "@/lib/api";
import { resolvePreselection } from "@/lib/application";
import { getDictionary } from "@/lib/i18n";
import type { Locale } from "@/lib/i18n/locales";

type Props = { locale: Locale; audience: "school" | "professional"; formation?: string; campus?: string };

export async function ApplicationPage({ locale, audience, formation, campus }: Props) {
  const [offerings, site] = await Promise.all([api.offerings(audience, locale), api.site(locale)]);
  const campuses = site.places.filter((place) => place.kind === "campus");
  // La formation demandée n'est qu'un indice : toute erreur revient à « pas de présélection ».
  const program = formation ? await api.program(formation, locale).catch(() => null) : null;
  const programOfferingIds = (program?.cohorts?.flatMap((c) => c.offerings ?? []) ?? []).map((o) => o.id);
  const { application: t, common } = getDictionary(locale);
  const { campusId, offeringId, notice } = resolvePreselection({ offerings, campuses, campusSlug: campus, programOfferingIds, notOffered: t.notOffered });
  const professional = audience === "professional";
  const whatsapp = site.settings.whatsapp?.replace(/[^0-9]/g, "");

  return (
    <>
      <PageHeader
        eyebrow={professional ? t.professionalEyebrow : t.eyebrow}
        title={professional ? t.professionalTitle : t.title}
        text={offerings.length > 0 ? t.intro : null}
        accent={professional ? "var(--color-hmi)" : undefined}
      />
      <div className="mx-auto max-w-4xl px-4 pb-24 sm:px-6">
        {offerings.length === 0 ? (
          <div className="rounded-[2rem] border border-line bg-night-2 p-8 sm:p-12">
            <p className="display text-2xl sm:text-3xl">{t.closedTitle}</p>
            <p className="mt-4 max-w-2xl text-ink-muted">
              {professional ? t.professionalRecruitment : t.nextSessions}
              {t.keepPosted(campuses.length > 1)}
            </p>
            <div className="mt-8 flex flex-wrap gap-3">
              {whatsapp && <ButtonLink href={`https://wa.me/${whatsapp}`}>{common.writeUsOnWhatsApp}</ButtonLink>}
              <ButtonLink href="/contact" variant={whatsapp ? "secondary" : "primary"}>{common.contactUs}</ButtonLink>
              <ButtonLink href={professional ? "/emsi/professionnels" : "/emsi/formations"} variant="secondary">{t.seePrograms}</ButtonLink>
            </div>
          </div>
        ) : (
          <ApplicationForm offerings={offerings} audience={audience} campuses={campuses} preselected={offeringId} preselectedCampus={campusId} notice={notice} wanted={programOfferingIds} />
        )}
      </div>
    </>
  );
}
