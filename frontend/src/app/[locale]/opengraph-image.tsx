import { ImageResponse } from "next/og";

/** Aperçu affiché quand un lien du site est partagé (WhatsApp, Facebook…), dans la charte « Plein feux ». */
export const alt = "EMSI — École des Métiers du Son et de l'Image, Dakar et Saint-Louis";
export const size = { width: 1200, height: 630 };
export const contentType = "image/png";

export default function OpenGraphImage() {
  const bars = [0.35, 0.6, 0.9, 0.7, 1, 0.85, 0.55, 0.75, 0.95, 0.6, 0.45, 0.8, 0.65, 0.9, 0.5, 0.7, 0.4, 0.6];
  return new ImageResponse(
    (
      <div style={{ width: "100%", height: "100%", display: "flex", flexDirection: "column", justifyContent: "space-between", padding: 72, background: "#07070a", color: "#f5f2ec", position: "relative" }}>
        <div style={{ position: "absolute", inset: 0, display: "flex", background: "radial-gradient(60% 70% at 25% 0%, rgba(255,122,26,0.35), transparent 70%), radial-gradient(50% 60% at 85% 10%, rgba(139,108,255,0.3), transparent 70%)" }} />
        <div style={{ display: "flex", alignItems: "center", gap: 14, fontSize: 24, letterSpacing: 6, textTransform: "uppercase", color: "#a7a3b2" }}>
          <div style={{ width: 14, height: 14, borderRadius: 7, background: "#ff3b30" }} />
          Dakar · Saint-Louis
        </div>
        <div style={{ display: "flex", flexDirection: "column" }}>
          <div style={{ fontSize: 150, fontWeight: 800, letterSpacing: -6, lineHeight: 1 }}>EMSI</div>
          <div style={{ fontSize: 40, marginTop: 18, maxWidth: 900 }}>École des Métiers du Son et de l&apos;Image</div>
        </div>
        <div style={{ display: "flex", alignItems: "flex-end", justifyContent: "space-between" }}>
          <div style={{ fontSize: 26, color: "#ff7a1a" }}>Son · Image · Lumière · Design · Scène</div>
          <div style={{ display: "flex", alignItems: "flex-end", gap: 6, height: 90 }}>
            {bars.map((h, i) => <div key={i} style={{ width: 10, height: 90 * h, borderRadius: 3, background: i % 3 === 0 ? "#8b6cff" : "#ff7a1a" }} />)}
          </div>
        </div>
      </div>
    ),
    size,
  );
}
