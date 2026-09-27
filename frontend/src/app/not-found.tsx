import type { Metadata } from "next";
import { ButtonLink } from "@/components/ui/ButtonLink";

export const metadata: Metadata = { title: "Page introuvable" };

export default function NotFound() {
  return (
    <div className="beam mx-auto max-w-3xl px-4 py-32 text-center sm:px-6">
      <p className="cartel">Erreur 404</p>
      <h1 className="mt-4 font-display text-5xl">Cette salle est plongée dans le noir.</h1>
      <p className="mt-4 text-ink-muted">La page demandée n&apos;existe pas ou a été déplacée.</p>
      <div className="mt-10 flex justify-center gap-3"><ButtonLink href="/musee" variant="secondary">Plan du musée</ButtonLink><ButtonLink href="/">Accueil</ButtonLink></div>
    </div>
  );
}
