import type { Metadata } from "next";
import { CtaBlock } from "@/components/blocks/ContentBlocks";
import { PageHeader } from "@/components/ui/PageHeader";
import { UniversesShowcase } from "@/components/universe/UniversesShowcase";
import { api } from "@/lib/api";

export const metadata: Metadata = {
  title: "Les univers",
  description: "Son, image (vidéo et photo), infographie et design, scène (régie et lumière), et bientôt cinéma : les univers de formation de l'EMSI à Dakar.",
};

export default async function UniversesPage() {
  const universes = await api.rooms();

  return (
    <>
      <PageHeader eyebrow="Les univers de l'EMSI" title="Trouvez votre lumière" text="Chaque univers réunit des filières, des outils et des métiers. Choisissez celui qui vous fait vibrer : sa page vous dit ce que vous apprendrez et où cela peut vous mener." />
      <UniversesShowcase universes={universes} />
      <CtaBlock data={{ title: "Votre univers vous attend", text: "Candidatez en ligne en quelques minutes, ou posez-nous vos questions.", buttons: [{ label: "Candidater", url: "/candidater", style: "primary" }, { label: "Nous contacter", url: "/contact", style: "secondary" }] }} />
    </>
  );
}
