"use client";

import { useEffect, useRef, type MutableRefObject, type RefObject } from "react";
import { useReducedMotion } from "@/components/motion/useReducedMotion";

/** Couleurs des rubans : l'orange et le violet du logo, le bleu des projecteurs HMI. */
const RIBBONS = [
  { hue: "255 122 26", amp: 0.16, freq: 1.3, speed: 0.22, offset: 0.0, y: 0.52 },
  { hue: "139 108 255", amp: 0.13, freq: 1.8, speed: 0.17, offset: 1.7, y: 0.56 },
  { hue: "63 208 255", amp: 0.1, freq: 2.4, speed: 0.28, offset: 3.1, y: 0.6 },
  { hue: "255 176 32", amp: 0.08, freq: 3.1, speed: 0.34, offset: 4.4, y: 0.5 },
  { hue: "255 79 163", amp: 0.07, freq: 1.1, speed: 0.13, offset: 5.2, y: 0.64 },
  { hue: "139 108 255", amp: 0.05, freq: 4.2, speed: 0.41, offset: 0.9, y: 0.58 },
];

export type Pointer = { x: number; y: number; energy: number };

/**
 * Rubans de lumière : des ondes sonores devenues lumière, dessinées sur un canvas et déformées
 * vers la main du visiteur. Suspendus hors écran, figés si l'internaute limite les animations.
 * `follow` se branche sur `onPointerMove` de la section et renvoie la position (0–1).
 */
export function useLightRibbons(
  sectionRef: RefObject<HTMLElement | null>,
  canvasRef: RefObject<HTMLCanvasElement | null>,
): { pointer: MutableRefObject<Pointer>; follow: (event: React.PointerEvent<HTMLElement>) => { x: number; y: number } } {
  const reducedMotion = useReducedMotion();
  const pointer = useRef<Pointer>({ x: 0.62, y: 0.55, energy: 0 });

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
  }, [reducedMotion, sectionRef, canvasRef]);


  function follow(event: React.PointerEvent<HTMLElement>) {
    const rect = event.currentTarget.getBoundingClientRect();
    const x = (event.clientX - rect.left) / rect.width;
    const y = (event.clientY - rect.top) / rect.height;
    const p = pointer.current;
    p.energy = Math.min(1, p.energy + Math.hypot(x - p.x, y - p.y) * 4);
    p.x = x;
    p.y = y;
    return { x, y };
  }

  return { pointer, follow };
}
