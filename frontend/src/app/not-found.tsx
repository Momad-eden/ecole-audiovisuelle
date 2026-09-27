import type { Metadata } from "next";
import { ButtonLink } from "@/components/ui/ButtonLink";

export const metadata: Metadata = { title: "Page introuvable" };

export default function NotFound() {
  return (
    <div className="beam mx-auto max-w-3xl px-4 pb-32 pt-44 text-center sm:px-6">
      <p className="cartel">Erreur 404</p>
      <h1 className="display mt-4 text-[clamp(2.4rem,6vw,4.5rem)] text-balance">Noir complet sur le plateau.</h1>
      <p className="mt-4 text-ink-muted">La page demandée n&apos;existe pas ou a été déplacée.</p>
      <div className="mt-10 flex justify-center gap-3"><ButtonLink href="/univers" variant="secondary">Les univers</ButtonLink><ButtonLink href="/">Accueil</ButtonLink></div>
    </div>
  );
}
