import type { CSSProperties } from "react";
import type { UniverseVisualKind } from "@/lib/types";
import { cn } from "@/lib/utils";

/**
 * Signature animée d'un univers, dessinée en SVG dans la couleur --accent.
 * Purement décorative (aria-hidden) ; animations CSS figées en mouvement réduit
 * et suspendues hors écran par <InView>.
 */
export function UniverseVisual({ kind, className }: { kind: UniverseVisualKind; className?: string }) {
  const Visual = VISUALS[kind] ?? SoundVisual;
  return (
    <svg viewBox="0 0 400 300" className={cn("h-full w-full text-[var(--accent-ink)]", className)} aria-hidden focusable="false" preserveAspectRatio="xMidYMid meet">
      <Visual />
    </svg>
  );
}

const mono = { fontFamily: "var(--font-mono)", fontSize: 9, letterSpacing: "0.12em" } as const;
const delay = (seconds: number): CSSProperties => ({ animationDelay: `${seconds}s` });

/** Son : spectre de fréquences et oscilloscope. */
function SoundVisual() {
  const bars = [0.35, 0.6, 0.9, 0.7, 1, 0.85, 0.55, 0.75, 0.95, 0.6, 0.45, 0.8, 0.65, 0.9, 0.5, 0.7, 0.4, 0.6, 0.35, 0.5, 0.3, 0.45, 0.25, 0.35];
  const wave = Array.from({ length: 33 }, (_, i) => `${i === 0 ? "M" : "L"}${i * 25} ${(70 + Math.sin(i * 0.9) * 26 * Math.cos(i * 0.23)).toFixed(1)}`).join(" ");
  return (
    <g>
      {[60, 110, 160, 210].map((y) => <line key={y} x1="20" x2="380" y1={y} y2={y} stroke="currentColor" strokeOpacity="0.08" />)}
      <g opacity="0.9">
        <clipPath id="uv-sound-clip"><rect x="20" y="30" width="360" height="90" /></clipPath>
        <g clipPath="url(#uv-sound-clip)">
          <path className="uv-wave" d={`${wave} M800 70`} transform="translate(20 0)" fill="none" stroke="currentColor" strokeWidth="1.6" />
        </g>
      </g>
      {bars.map((h, i) => (
        <rect key={i} className="uv-bar" x={24 + i * 15} y={Math.round(270 - 120 * h)} width="9" height={Math.round(120 * h)} rx="2" fill="currentColor" fillOpacity={Number((0.35 + h * 0.6).toFixed(2))} style={{ ...delay(Number((-i * 0.13).toFixed(2))), animationDuration: `${(0.8 + (i % 5) * 0.18).toFixed(2)}s` }} />
      ))}
      <text x="22" y="24" fill="currentColor" style={mono}>● MIX L/R</text>
      <text x="300" y="24" fill="currentColor" fillOpacity="0.6" style={mono}>-6 dB FS</text>
      <line x1="20" x2="380" y1="272" y2="272" stroke="currentColor" strokeOpacity="0.4" />
      <text x="22" y="292" fill="currentColor" fillOpacity="0.6" style={mono}>20 Hz</text>
      <text x="340" y="292" fill="currentColor" fillOpacity="0.6" style={mono}>20 kHz</text>
    </g>
  );
}

/** Image : viseur de caméra (repères, grille des tiers, mise au point, iris). */
function ImageVisual() {
  const corner = (x: number, y: number, dx: number, dy: number) => `M${x} ${y + dy * 26} L${x} ${y} L${x + dx * 26} ${y}`;
  const blades = Array.from({ length: 6 }, (_, i) => i * 60);
  return (
    <g fill="none" stroke="currentColor">
      {[corner(24, 24, 1, 1), corner(376, 24, -1, 1), corner(24, 276, 1, -1), corner(376, 276, -1, -1)].map((d) => <path key={d} d={d} strokeWidth="2.5" />)}
      <g strokeOpacity="0.14">
        <line x1="137" x2="137" y1="24" y2="276" /><line x1="263" x2="263" y1="24" y2="276" />
        <line x1="24" x2="376" y1="108" y2="108" /><line x1="24" x2="376" y1="192" y2="192" />
      </g>
      <g className="uv-iris" transform="translate(200 150)" strokeOpacity="0.5">
        <circle r="58" strokeOpacity="0.35" />
        {blades.map((a) => <path key={a} d="M0 -58 L30 -12" transform={`rotate(${a})`} />)}
      </g>
      <g className="uv-focus">
        <path d="M172 128 h-10 v10 M228 128 h10 v10 M172 172 h-10 v-10 M228 172 h10 v-10" strokeWidth="2" />
      </g>
      <circle className="rec-dot" cx="42" cy="46" r="5" fill="var(--color-rec)" stroke="none" />
      <text x="54" y="50" fill="currentColor" stroke="none" style={mono}>REC</text>
      <text x="300" y="50" fill="currentColor" stroke="none" fillOpacity="0.8" style={mono}>4K · 50P</text>
      <text x="42" y="262" fill="currentColor" stroke="none" fillOpacity="0.8" style={mono}>ISO 800 · f/2.8</text>
      <text x="268" y="262" fill="currentColor" stroke="none" style={mono}>00:12:04:18</text>
    </g>
  );
}

/** Infographie & design : courbe de Bézier qui se trace, repères d'impression, nuancier. */
function DesignVisual() {
  const dots = Array.from({ length: 11 * 8 }, (_, i) => [34 + (i % 11) * 33, 36 + Math.floor(i / 11) * 33]);
  const reg = (x: number, y: number) => (
    <g key={`${x}-${y}`} transform={`translate(${x} ${y})`} fill="none" stroke="currentColor" strokeOpacity="0.55">
      <circle r="7" /><line x1="-11" x2="11" /><line y1="-11" y2="11" />
    </g>
  );
  return (
    <g>
      {dots.map(([x, y]) => <circle key={`${x}-${y}`} cx={x} cy={y} r="1" fill="currentColor" fillOpacity="0.18" />)}
      {[reg(22, 22), reg(378, 22), reg(22, 278), reg(378, 278)]}
      <path className="uv-draw" pathLength={1} d="M50 220 C 110 40, 190 40, 205 150 S 300 260, 350 80" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" />
      <g stroke="currentColor" strokeOpacity="0.6">
        <line x1="205" y1="150" x2="150" y2="96" /><line x1="205" y1="150" x2="258" y2="204" />
      </g>
      <g className="uv-handle"><rect x="145" y="91" width="10" height="10" fill="var(--color-night)" stroke="currentColor" /></g>
      <rect x="199" y="144" width="12" height="12" fill="currentColor" />
      <g className="uv-handle" style={delay(-2)}><rect x="253" y="199" width="10" height="10" fill="var(--color-night)" stroke="currentColor" /></g>
      <rect x="45" y="215" width="10" height="10" fill="var(--color-night)" stroke="currentColor" />
      <rect x="345" y="75" width="10" height="10" fill="var(--color-night)" stroke="currentColor" />
      {["#00b7eb", "#ff4fa3", "#ffe600", "#f5f2ec"].map((color, i) => <rect key={color} x={44 + i * 22} y="250" width="16" height="16" rx="2" fill={color} fillOpacity={i === 3 ? 0.85 : 1} />)}
      <text x="250" y="263" fill="currentColor" fillOpacity="0.7" style={mono}>1920 × 1080</text>
    </g>
  );
}

/** Scène : projecteurs sur un pont, faisceaux et console DMX. */
function StageVisual() {
  const fixtures = [70, 140, 210, 280, 350];
  const faders = [
    { x: 262, from: 0, to: -44, dur: 3.2 }, { x: 282, from: -30, to: 4, dur: 2.6 }, { x: 302, from: -10, to: -50, dur: 3.8 },
    { x: 322, from: -48, to: -6, dur: 2.9 }, { x: 342, from: -20, to: -46, dur: 3.4 }, { x: 362, from: 2, to: -34, dur: 2.4 },
  ];
  return (
    <g>
      <defs>
        <linearGradient id="uv-beam" x1="0" x2="0" y1="0" y2="1">
          <stop offset="0" stopColor="currentColor" stopOpacity="0.75" />
          <stop offset="1" stopColor="currentColor" stopOpacity="0" />
        </linearGradient>
      </defs>
      <line x1="30" x2="390" y1="28" y2="28" stroke="currentColor" strokeOpacity="0.5" strokeWidth="3" />
      {fixtures.map((x, i) => (
        <g key={x}>
          <path className="uv-beam" d={`M${x - 6} 38 L${x - 46} 250 L${x + 46} 250 L${x + 6} 38 Z`} fill="url(#uv-beam)" style={{ ...delay(-i * 1.3), animationDuration: `${4 + (i % 3)}s`, mixBlendMode: "screen" }} />
          <rect x={x - 9} y="26" width="18" height="14" rx="3" fill="var(--color-night-3)" stroke="currentColor" />
        </g>
      ))}
      <ellipse cx="170" cy="252" rx="150" ry="14" fill="currentColor" fillOpacity="0.12" />
      <rect x="250" y="196" width="126" height="92" rx="6" fill="var(--color-night-2)" stroke="currentColor" strokeOpacity="0.5" />
      {faders.map((f) => (
        <g key={f.x}>
          <line x1={f.x} x2={f.x} y1="210" y2="276" stroke="currentColor" strokeOpacity="0.3" strokeWidth="2" />
          <rect className="uv-fader" x={f.x - 6} y="266" width="12" height="7" rx="1.5" fill="currentColor" style={{ ["--f-from" as string]: `${f.from}px`, ["--f-to" as string]: `${f.to}px`, ["--f-dur" as string]: `${f.dur}s` }} />
        </g>
      ))}
      <text x="36" y="20" fill="currentColor" fillOpacity="0.8" style={mono}>DMX 512 · UNIVERS 1</text>
    </g>
  );
}

/** Cinéma : pellicule qui défile et clap (bientôt). */
function CinemaVisual() {
  const frames = Array.from({ length: 12 }, (_, i) => i * 70);
  const holes = Array.from({ length: 48 }, (_, i) => i * 17.5);
  return (
    <g>
      <g transform="translate(0 70) rotate(-6 200 80)">
        <clipPath id="uv-film-clip"><rect x="-20" y="0" width="440" height="130" /></clipPath>
        <g clipPath="url(#uv-film-clip)">
          <g className="uv-film">
            <rect x="0" y="0" width="840" height="130" fill="var(--color-night-3)" />
            {holes.map((x) => (
              <g key={x}><rect x={x + 4} y="8" width="8" height="10" rx="2" fill="var(--color-night)" /><rect x={x + 4} y="112" width="8" height="10" rx="2" fill="var(--color-night)" /></g>
            ))}
            {frames.map((x, i) => (
              <rect key={x} x={x + 6} y="26" width="58" height="78" rx="3" fill="currentColor" fillOpacity={Number((0.12 + (i % 4) * 0.08).toFixed(2))} stroke="currentColor" strokeOpacity="0.5" />
            ))}
          </g>
        </g>
      </g>
      <g transform="translate(270 196)">
        <rect x="0" y="22" width="100" height="62" rx="4" fill="var(--color-night-2)" stroke="currentColor" strokeOpacity="0.6" />
        <g transform="rotate(-14 0 22)">
          <rect x="0" y="6" width="100" height="16" fill="var(--color-ink)" />
          {[0, 1, 2, 3, 4].map((i) => <path key={i} d={`M${i * 22} 6 L${i * 22 + 12} 6 L${i * 22 + 4} 22 L${i * 22 - 8} 22 Z`} fill="var(--color-night)" />)}
        </g>
        <text x="10" y="50" fill="currentColor" style={mono}>SCÈNE 1</text>
        <text x="10" y="70" fill="currentColor" fillOpacity="0.7" style={mono}>PRISE 1</text>
      </g>
      <text x="24" y="40" fill="currentColor" style={mono}>● 24 FPS</text>
      <text x="24" y="268" fill="currentColor" fillOpacity="0.7" style={mono}>BIENTÔT À L&apos;EMSI</text>
    </g>
  );
}

const VISUALS: Record<UniverseVisualKind, () => React.JSX.Element> = {
  sound: SoundVisual,
  image: ImageVisual,
  design: DesignVisual,
  stage: StageVisual,
  cinema: CinemaVisual,
};
