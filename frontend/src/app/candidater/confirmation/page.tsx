import type { Metadata } from "next";
import { ButtonLink } from "@/components/ui/ButtonLink";

export const metadata: Metadata = { title: "Candidature envoyée", robots: { index: false } };

type Props = { searchParams: Promise<{ ref?: string }> };

export default async function ConfirmationPage({ searchParams }: Props) {
  const { ref } = await searchParams;

  return (
    <div className="beam mx-auto max-w-3xl px-4 py-24 text-center sm:px-6">
      <p className="cartel">Candidature envoyée</p>
      <h1 className="mt-4 font-display text-5xl">Merci, nous avons bien reçu votre dossier.</h1>
      {ref && <p className="mt-6 text-lg">Votre numéro de dossier : <strong className="font-mono text-amber">{ref}</strong></p>}
      <p className="mx-auto mt-4 max-w-xl text-ink-muted">Notez-le : il vous sera demandé dans nos échanges. Si vous avez indiqué une adresse e-mail, un accusé de réception vous a été envoyé. Notre équipe vous recontactera pour la suite.</p>
      <div className="mt-10 flex justify-center gap-3"><ButtonLink href="/musee" variant="secondary">Visiter le musée</ButtonLink><ButtonLink href="/">Retour à l&apos;accueil</ButtonLink></div>
    </div>
  );
}
