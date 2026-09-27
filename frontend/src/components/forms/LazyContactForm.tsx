"use client";

import dynamic from "next/dynamic";

/**
 * Formulaire de contact chargé à la demande : la validation (zod, react-hook-form) n'est
 * téléchargée que par les pages qui affichent réellement le formulaire. Rendu côté navigateur
 * seulement : un clic avant le chargement ne peut pas envoyer le formulaire sans validation.
 */
export const LazyContactForm = dynamic(() => import("./ContactForm").then((m) => m.ContactForm), {
  ssr: false,
  loading: () => <div className="min-h-96 rounded-3xl border border-line bg-night-2" aria-busy="true" />,
});
