"use client";

/**
 * Erreur dans le layout racine lui-même : Next remplace alors tout le document (ni styles, ni langue connue).
 * Message court en français et en anglais côte à côte, styles en ligne aux couleurs du thème sombre.
 */
export default function GlobalError({ reset }: { error: Error & { digest?: string }; reset: () => void }) {
  const button = { minHeight: 48, padding: "0 28px", borderRadius: 999, border: 0, background: "#ff7a1a", color: "#07070a", fontWeight: 600, cursor: "pointer" } as const;
  return (
    <html lang="fr">
      <body style={{ margin: 0, minHeight: "100dvh", display: "grid", placeItems: "center", background: "#07070a", color: "#f5f2ec", fontFamily: "system-ui, sans-serif", padding: 24, textAlign: "center" }}>
        <title>EMSI — Incident technique · Technical issue</title>
        <main>
          <h1 style={{ fontSize: "clamp(1.8rem, 5vw, 3rem)", margin: 0 }}>Coupure de courant momentanée.</h1>
          <p style={{ color: "#a7a3b2" }}>La page n&apos;a pas pu s&apos;afficher. Réessayez dans un instant.</p>
          <div lang="en" style={{ marginTop: 32 }}>
            <p style={{ fontSize: "clamp(1.4rem, 4vw, 2.2rem)", margin: 0, fontWeight: 700 }}>Momentary power cut.</p>
            <p style={{ color: "#a7a3b2" }}>The page could not be displayed. Please try again in a moment.</p>
          </div>
          <button type="button" onClick={reset} style={{ ...button, marginTop: 24 }}>
            Réessayer · <span lang="en">Try again</span>
          </button>
        </main>
      </body>
    </html>
  );
}
