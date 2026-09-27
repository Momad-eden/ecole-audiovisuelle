import type { CSSProperties } from "react";

/**
 * Décors animés des héros « Studio » et « Événementiel », en SVG + CSS (aucune image).
 * Décoratifs (aria-hidden), figés en mouvement réduit, suspendus hors écran par <InView>.
 */

const mono = { fontFamily: "var(--font-mono)", letterSpacing: "0.18em" } as const;
const vars = (entries: Record<string, string | number>) => Object.fromEntries(Object.entries(entries).map(([k, v]) => [`--${k}`, String(v)])) as CSSProperties;

/** Console de mixage de régie : tranches, vumètres, faders et voyant ON AIR. */
export function ConsoleArt() {
  const strips = Array.from({ length: 22 }, (_, i) => i);
  const x0 = 70;
  const step = 44;

  return (
    <svg viewBox="0 0 1200 560" preserveAspectRatio="xMidYMax slice" className="absolute inset-x-0 bottom-0 h-[72%] w-full" aria-hidden focusable="false">
      <defs>
        <linearGradient id="console-body" x1="0" x2="0" y1="0" y2="1">
          <stop offset="0" stopColor="var(--color-night-3)" />
          <stop offset="1" stopColor="var(--color-night)" />
        </linearGradient>
        <linearGradient id="meter" x1="0" x2="0" y1="1" y2="0">
          <stop offset="0" stopColor="#2ee6a8" />
          <stop offset="0.65" stopColor="var(--color-gold)" />
          <stop offset="1" stopColor="var(--color-rec)" />
        </linearGradient>
        <radialGradient id="onair-glow" cx="50%" cy="50%" r="50%">
          <stop offset="0" stopColor="var(--color-rec)" stopOpacity="0.55" />
          <stop offset="1" stopColor="var(--color-rec)" stopOpacity="0" />
        </radialGradient>
      </defs>

      {/* Voyant ON AIR, pulsation lente (jamais de clignotement rapide). */}
      <g transform="translate(980 40)">
        <circle className="onair-glow" cx="80" cy="26" r="90" fill="url(#onair-glow)" />
        <rect width="160" height="52" rx="10" fill="var(--color-night-2)" stroke="var(--color-rec)" strokeWidth="2" />
        <text x="80" y="34" textAnchor="middle" fill="var(--color-rec)" fontSize="22" fontWeight="700" style={mono}>ON AIR</text>
      </g>

      <path d="M20 250 L1180 250 L1200 560 L0 560 Z" fill="url(#console-body)" stroke="var(--color-line)" />
      <line x1="30" x2="1170" y1="300" y2="300" stroke="var(--color-line)" />

      {strips.map((i) => {
        const x = x0 + i * step;
        const level = 0.35 + ((i * 37) % 60) / 100;
        return (
          <g key={i}>
            {[0, 1, 2].map((k) => (
              <g key={k} transform={`translate(${x} ${272 + k * 0})`}>
                <circle cx={-10 + k * 10} cy="0" r="3.2" fill="none" stroke="var(--color-ink)" strokeOpacity="0.35" />
              </g>
            ))}
            {/* Vumètre de la tranche */}
            <rect x={x - 5} y="312" width="10" height="70" rx="2" fill="var(--color-night)" />
            <rect className="meter-bar" x={x - 4} y="313" width="8" height="68" rx="1.5" fill="url(#meter)"
              style={{ ...vars({ "m-lo": (level * 0.35).toFixed(2), "m-hi": Math.min(1, level + 0.25).toFixed(2) }), animationDuration: `${(0.5 + ((i * 13) % 7) / 10).toFixed(2)}s`, animationDelay: `${(-i * 0.17).toFixed(2)}s` }} />
            {/* Bouton mute, quelques voies allumées */}
            <rect x={x - 8} y="396" width="16" height="9" rx="2" fill={i % 5 === 2 ? "var(--color-gold)" : "var(--color-night-3)"} stroke="var(--color-line)" />
            {/* Fader */}
            <line x1={x} x2={x} y1="420" y2="530" stroke="var(--color-ink)" strokeOpacity="0.18" strokeWidth="3" strokeLinecap="round" />
            <rect className="uv-fader" x={x - 9} y="505" width="18" height="12" rx="2" fill="var(--color-ink)" fillOpacity="0.85"
              style={vars({ "f-from": `${-Math.round(level * 60)}px`, "f-to": `${-Math.round(level * 60 + 18)}px`, "f-dur": `${(3 + (i % 4) * 0.7).toFixed(1)}s` })} />
          </g>
        );
      })}

      {/* Section master : deux grands vumètres à aiguille */}
      {[0, 1].map((k) => (
        <g key={k} transform={`translate(${440 + k * 190} 110)`}>
          <rect width="170" height="110" rx="12" fill="var(--color-night-2)" stroke="var(--color-line)" />
          <path d="M25 90 A60 60 0 0 1 145 90" fill="none" stroke="var(--color-ink)" strokeOpacity="0.4" />
          <path d="M118 52 A60 60 0 0 1 145 90" fill="none" stroke="var(--color-rec)" strokeWidth="3" />
          <line className="vu-needle" x1="85" y1="96" x2="85" y2="40" stroke="var(--color-gold)" strokeWidth="2.5" strokeLinecap="round"
            style={{ animationDelay: `${-k * 0.4}s` }} />
          <circle cx="85" cy="96" r="5" fill="var(--color-gold)" />
          <text x="85" y="24" textAnchor="middle" fill="var(--color-ink)" fillOpacity="0.6" fontSize="12" style={mono}>{k === 0 ? "L" : "R"} · VU</text>
        </g>
      ))}
    </svg>
  );
}

/** Deux line arrays suspendus, subwoofers au sol et public, sous des faisceaux ambrés. */
export function LineArrayArt() {
  const cabinets = Array.from({ length: 9 }, (_, i) => i);
  const array = (x: number, flip: boolean) => (
    <g transform={`translate(${x} 0) ${flip ? "scale(-1 1)" : ""}`}>
      <line x1="0" y1="0" x2="0" y2="46" stroke="var(--color-ink)" strokeOpacity="0.4" />
      <line x1="60" y1="0" x2="60" y2="46" stroke="var(--color-ink)" strokeOpacity="0.4" />
      <rect x="-10" y="44" width="80" height="8" rx="2" fill="var(--color-ink)" fillOpacity="0.55" />
      {cabinets.map((i) => {
        const tilt = i * i * 0.55;
        return (
          <g key={i} transform={`translate(${i * i * 0.9} ${56 + i * 30}) rotate(${tilt} 30 0)`}>
            <path d="M0 0 H62 L58 26 H4 Z" fill="var(--color-night-3)" stroke="var(--color-ink)" strokeOpacity="0.35" />
            <rect x="10" y="7" width="42" height="11" rx="2" fill="var(--color-night)" />
            <circle className="cab-led" cx="56" cy="5" r="1.8" fill="var(--color-gold)" style={{ animationDelay: `${-i * 0.23}s` }} />
          </g>
        );
      })}
    </g>
  );

  return (
    <svg viewBox="0 0 1200 600" preserveAspectRatio="xMidYMid slice" className="absolute inset-0 h-full w-full" aria-hidden focusable="false">
      <defs>
        <linearGradient id="crowd" x1="0" x2="0" y1="0" y2="1">
          <stop offset="0" stopColor="var(--color-night)" stopOpacity="0.2" />
          <stop offset="1" stopColor="var(--color-night)" />
        </linearGradient>
      </defs>
      <line x1="0" x2="1200" y1="14" y2="14" stroke="var(--color-ink)" strokeOpacity="0.3" strokeWidth="4" />
      {array(120, false)}
      {array(1080, true)}
      {[70, 1060].map((x) => (
        <g key={x} transform={`translate(${x} 470)`}>
          <rect width="90" height="60" rx="4" fill="var(--color-night-3)" stroke="var(--color-ink)" strokeOpacity="0.3" />
          <circle className="sub-cone" cx="45" cy="30" r="20" fill="none" stroke="var(--color-ink)" strokeOpacity="0.45" strokeWidth="2" />
          <rect y="-62" width="90" height="60" rx="4" fill="var(--color-night-3)" stroke="var(--color-ink)" strokeOpacity="0.3" />
          <circle className="sub-cone" cx="45" cy="-32" r="20" fill="none" stroke="var(--color-ink)" strokeOpacity="0.45" strokeWidth="2" style={{ animationDelay: "-0.2s" }} />
        </g>
      ))}
      {/* Public, bras levés */}
      <path fill="url(#crowd)" d={`M0 600 V560 ${Array.from({ length: 40 }, (_, i) => {
        const x = i * 30;
        const h = 530 + ((i * 17) % 20);
        return `Q${x + 8} ${h - 10} ${x + 15} ${h} ${i % 6 === 2 ? `L${x + 18} ${h - 60} L${x + 22} ${h - 60} L${x + 21} ${h}` : ""} Q${x + 24} ${h + 6} ${x + 30} 560`;
      }).join(" ")} V600 Z`} />
    </svg>
  );
}
