"use client";

import { useEffect, useRef, useState } from "react";
import { Volume2, VolumeX } from "lucide-react";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { useReducedMotion } from "@/components/motion/useReducedMotion";
import { cn, frenchSpacing } from "@/lib/utils";
import type { HeroData } from "./types";

/** Couleurs des rubans : l'orange et le violet du logo, le bleu des projecteurs HMI. */
const RIBBONS = [
  { hue: "255 122 26", amp: 0.16, freq: 1.3, speed: 0.22, offset: 0.0, y: 0.52 },
  { hue: "139 108 255", amp: 0.13, freq: 1.8, speed: 0.17, offset: 1.7, y: 0.56 },
  { hue: "63 208 255", amp: 0.1, freq: 2.4, speed: 0.28, offset: 3.1, y: 0.6 },
  { hue: "255 176 32", amp: 0.08, freq: 3.1, speed: 0.34, offset: 4.4, y: 0.5 },
  { hue: "255 79 163", amp: 0.07, freq: 1.1, speed: 0.13, offset: 5.2, y: 0.64 },
  { hue: "139 108 255", amp: 0.05, freq: 4.2, speed: 0.41, offset: 0.9, y: 0.58 },
];

type Pointer = { x: number; y: number; energy: number };

/**
 * « Œuvre » : un héros conçu comme une pièce d'exposition. Des rubans de lumière, comme des
 * ondes sonores devenues lumière, se déforment vers la main du visiteur ; le titre est rempli
 * par la photo (ou par la lumière) ; un cartel de musée indique la « fréquence » sous le pointeur.
 * Le son est facultatif, lancé par un bouton, jamais automatiquement.
 */
export function ArtHero({ data, first }: { data: HeroData; first: boolean }) {
  const reducedMotion = useReducedMotion();
  const sectionRef = useRef<HTMLElement>(null);
  const canvasRef = useRef<HTMLCanvasElement>(null);
  const pointer = useRef<Pointer>({ x: 0.62, y: 0.55, energy: 0 });
  const readoutRef = useRef<HTMLSpanElement>(null);
  const sound = useRef<{ context: AudioContext; gain: GainNode; oscillators: OscillatorNode[]; filter: BiquadFilterNode } | null>(null);
  const [listening, setListening] = useState(false);
  const Heading = first ? "h1" : "h2";

  // Rubans de lumière (canvas 2D, fusion additive), suspendus hors écran, figés en mouvement réduit.
  useEffect(() => {
    const canvas = canvasRef.current;
    const section = sectionRef.current;
    if (!canvas || !section) return;
    const context = canvas.getContext("2d");
    if (!context) return;

    let width = 0;
    let height = 0;
    let frame = 0;
    let visible = true;
    const ratio = Math.min(window.devicePixelRatio || 1, 1.5);

    const resize = () => {
      width = section.clientWidth;
      height = section.clientHeight;
      canvas.width = Math.round(width * ratio);
      canvas.height = Math.round(height * ratio);
      context.setTransform(ratio, 0, 0, ratio, 0, 0);
    };

    const draw = (time: number) => {
      const t = time / 1000;
      const p = pointer.current;
      p.energy *= 0.96;
      context.clearRect(0, 0, width, height);
      context.globalCompositeOperation = "lighter";
      const steps = Math.max(80, Math.round(width / 9));

      for (const ribbon of RIBBONS) {
        const points: [number, number][] = [];
        for (let i = 0; i <= steps; i++) {
          const u = i / steps;
          const near = Math.exp(-Math.pow((u - p.x) * 3.2, 2));
          const swell = 1 + near * (0.9 + p.energy * 2.2);
          const wave =
            Math.sin(u * Math.PI * 2 * ribbon.freq + t * ribbon.speed * 6 + ribbon.offset) * 0.62 +
            Math.sin(u * Math.PI * 2 * ribbon.freq * 2.3 - t * ribbon.speed * 4 + ribbon.offset * 1.7) * 0.38;
          const pull = (p.y - ribbon.y) * near * 0.55;
          points.push([u * width, (ribbon.y + pull + wave * ribbon.amp * swell) * height]);
        }
        for (const [lineWidth, alpha] of [[26, 0.035], [9, 0.09], [1.6, 0.75]] as const) {
          context.beginPath();
          points.forEach(([x, y], i) => (i === 0 ? context.moveTo(x, y) : context.lineTo(x, y)));
          context.strokeStyle = `rgb(${ribbon.hue} / ${alpha})`;
          context.lineWidth = lineWidth;
          context.lineCap = "round";
          context.stroke();
        }
      }
      context.globalCompositeOperation = "source-over";
      if (!reducedMotion && visible) frame = requestAnimationFrame(draw);
    };

    resize();
    draw(performance.now());
    const resizeObserver = new ResizeObserver(() => {
      resize();
      if (reducedMotion) draw(4000);
    });
    resizeObserver.observe(section);
    const intersection = new IntersectionObserver(([entry]) => {
      visible = entry.isIntersecting;
      cancelAnimationFrame(frame);
      if (visible && !reducedMotion) frame = requestAnimationFrame(draw);
    });
    intersection.observe(section);

    return () => {
      cancelAnimationFrame(frame);
      resizeObserver.disconnect();
      intersection.disconnect();
    };
  }, [reducedMotion]);

  // Arrêter proprement le son en quittant la page.
  useEffect(() => () => void sound.current?.context.close(), []);

  function follow(event: React.PointerEvent<HTMLElement>) {
    const rect = event.currentTarget.getBoundingClientRect();
    const x = (event.clientX - rect.left) / rect.width;
    const y = (event.clientY - rect.top) / rect.height;
    const p = pointer.current;
    p.energy = Math.min(1, p.energy + Math.hypot(x - p.x, y - p.y) * 4);
    p.x = x;
    p.y = y;
    const frequency = Math.round(110 * Math.pow(2, x * 3));
    if (readoutRef.current) readoutRef.current.textContent = `${frequency} Hz · x ${x.toFixed(2)} · y ${y.toFixed(2)}`;
    const s = sound.current;
    if (s) {
      const now = s.context.currentTime;
      s.oscillators.forEach((osc, i) => osc.frequency.setTargetAtTime(frequency * [1, 1.5, 2.01][i], now, 0.08));
      s.filter.frequency.setTargetAtTime(400 + (1 - y) * 3200, now, 0.1);
    }
  }

  function toggleSound() {
    if (sound.current) {
      const s = sound.current;
      s.gain.gain.setTargetAtTime(0, s.context.currentTime, 0.15);
      setTimeout(() => void s.context.close(), 500);
      sound.current = null;
      setListening(false);
      return;
    }
    const Context = window.AudioContext ?? (window as unknown as { webkitAudioContext: typeof AudioContext }).webkitAudioContext;
    const context = new Context();
    const gain = context.createGain();
    const filter = context.createBiquadFilter();
    filter.type = "lowpass";
    filter.frequency.value = 1400;
    gain.gain.value = 0;
    filter.connect(gain).connect(context.destination);
    const oscillators = (["sine", "triangle", "sine"] as OscillatorType[]).map((type, i) => {
      const osc = context.createOscillator();
      osc.type = type;
      osc.frequency.value = 220 * [1, 1.5, 2.01][i];
      const voice = context.createGain();
      voice.gain.value = [0.5, 0.18, 0.12][i];
      osc.connect(voice).connect(filter);
      osc.start();
      return osc;
    });
    gain.gain.setTargetAtTime(0.06, context.currentTime, 0.4);
    sound.current = { context, gain, oscillators, filter };
    setListening(true);
  }

  const image = data.image?.url;

  return (
    <section
      ref={sectionRef}
      onPointerMove={follow}
      className="art-hero relative isolate flex min-h-[92svh] flex-col overflow-hidden bg-night"
      style={data.accent ? { ["--accent" as string]: data.accent } : undefined}
    >
      <canvas ref={canvasRef} className="absolute inset-0 -z-10 h-full w-full" aria-hidden />
      <div className="absolute inset-0 -z-10 bg-[radial-gradient(70%_60%_at_50%_45%,transparent,var(--color-night)_92%)]" aria-hidden />

      {/* Cadre d'exposition : filet et repères d'angle. */}
      <div className="pointer-events-none absolute inset-4 border border-ink/15 sm:inset-6" aria-hidden>
        {["left-0 top-0 border-l-2 border-t-2", "right-0 top-0 border-r-2 border-t-2", "bottom-0 left-0 border-b-2 border-l-2", "bottom-0 right-0 border-b-2 border-r-2"].map((corner) => (
          <span key={corner} className={cn("absolute size-5 border-brand", corner)} />
        ))}
      </div>

      <div className="mx-auto flex w-full max-w-7xl flex-1 flex-col justify-end px-8 pb-12 pt-32 sm:px-12 sm:pb-16 lg:px-16">
        {data.eyebrow && <p className="cartel mb-6 text-ink/80">{data.eyebrow}</p>}
        <Heading
          className={cn("art-title display max-w-6xl text-balance", data.title.length <= 26 ? "text-[clamp(3.2rem,11vw,10.5rem)]" : "text-[clamp(2.6rem,7.5vw,7rem)]")}
          style={image ? { backgroundImage: `url("${image}")` } : undefined}
          data-image={image ? "true" : undefined}
        >
          {frenchSpacing(data.title)}
        </Heading>

        <div className="mt-10 grid gap-10 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
          <div>
            {data.subtitle && <p className="max-w-xl text-lg text-ink/85 sm:text-xl">{data.subtitle}</p>}
            <div className="mt-8 flex flex-wrap items-center gap-3">
              {(data.buttons ?? []).map((button) => (
                <ButtonLink key={button.url + button.label} href={button.url} size="lg" variant={button.style === "secondary" ? "secondary" : "primary"}>{button.label}</ButtonLink>
              ))}
              {!reducedMotion && (
                <button type="button" onClick={toggleSound} aria-pressed={listening}
                  className="inline-flex min-h-14 items-center gap-2 rounded-full border border-ink/25 px-6 text-sm font-semibold text-ink transition hover:border-brand hover:text-brand">
                  {listening ? <VolumeX className="size-5" aria-hidden /> : <Volume2 className="size-5" aria-hidden />}
                  {listening ? "Couper le son" : "Écouter l'œuvre"}
                </button>
              )}
            </div>
          </div>

          {/* Cartel de musée : l'œuvre, sa technique, et ce que « joue » la main du visiteur. */}
          <aside className="w-full max-w-xs border-l-2 border-brand bg-night/60 px-5 py-4 backdrop-blur-sm" aria-label="Cartel de l'œuvre">
            <p className="display text-base">{data.caption || "Ondes lumineuses"}</p>
            <p className="mt-1 text-sm text-ink-muted">Son, lumière et image · EMSI</p>
            <p className="cartel mt-3 tabular-nums" aria-hidden><span ref={readoutRef}>220 Hz · x 0.62 · y 0.55</span></p>
          </aside>
        </div>
      </div>
    </section>
  );
}
