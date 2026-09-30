"use client";

export default function Error({ reset }: { error: Error; reset: () => void }) {
  return (
    <div className="mx-auto max-w-3xl px-4 pb-32 pt-44 text-center sm:px-6">
      <p className="cartel">Incident technique</p>
      <h1 className="display mt-4 text-[clamp(2.2rem,5vw,4rem)] text-balance">Coupure de courant momentanée.</h1>
      <p className="mt-4 text-ink-muted">La page n&apos;a pas pu s&apos;afficher. Réessayez dans un instant.</p>
      <button type="button" onClick={reset} className="mt-10 min-h-12 rounded-full bg-brand px-8 font-semibold text-on-accent">Réessayer</button>
    </div>
  );
}
