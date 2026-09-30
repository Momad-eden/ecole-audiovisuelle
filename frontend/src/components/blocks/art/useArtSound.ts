"use client";

import { useEffect, useRef, useState } from "react";
import { sameOrigin } from "@/components/audio/peaks";

/**
 * Son de l'œuvre : le fichier choisi dans l'admin (joué en boucle), sinon trois oscillateurs
 * (note, quinte, octave). La main du visiteur règle la place stéréo, le filtre et l'intensité.
 */
type SoundGraph = {
  context: AudioContext;
  gain: GainNode;
  filter: BiquadFilterNode;
  panner: StereoPannerNode;
  oscillators: OscillatorNode[];
  source?: AudioBufferSourceNode;
};

/** Fichiers déjà décodés : on ne retélécharge pas le son à chaque clic. */
const decoded = new Map<string, Promise<ArrayBuffer>>();

/**
 * Son d'une œuvre interactive : lancé et coupé par un bouton, jamais automatiquement.
 * `steer` fait suivre la main du visiteur (x, y entre 0 et 1, énergie du geste).
 */
export function useArtSound(src?: string | null) {
  const sound = useRef<SoundGraph | null>(null);
  const [listening, setListening] = useState(false);
  const [loading, setLoading] = useState(false);

  // Arrêter proprement le son en quittant la page.
  useEffect(() => () => void sound.current?.context.close(), []);

  function steer(x: number, y: number, energy: number) {
    const s = sound.current;
    if (s) {
      const frequency = Math.round(110 * Math.pow(2, x * 3));
      const now = s.context.currentTime;
      s.oscillators.forEach((osc, i) => osc.frequency.setTargetAtTime(frequency * [1, 1.5, 2.01][i], now, 0.08));
      s.panner.pan.setTargetAtTime(x * 1.6 - 0.8, now, 0.12);
      // Fichier : le haut de l'écran ouvre le filtre (son brillant), le bas l'étouffe.
      s.filter.frequency.setTargetAtTime(s.source ? 300 * Math.pow(2, (1 - y) * 6) : 400 + (1 - y) * 3200, now, 0.1);
      if (s.source) s.gain.gain.setTargetAtTime(0.55 + energy * 0.35, now, 0.2);
    }
  }

  async function toggle() {
    if (sound.current) {
      const s = sound.current;
      s.gain.gain.setTargetAtTime(0, s.context.currentTime, 0.15);
      setTimeout(() => void s.context.close(), 600);
      sound.current = null;
      setListening(false);
      return;
    }

    const Context = window.AudioContext ?? (window as unknown as { webkitAudioContext: typeof AudioContext }).webkitAudioContext;
    const context = new Context();
    const gain = context.createGain();
    const filter = context.createBiquadFilter();
    const panner = context.createStereoPanner();
    filter.type = "lowpass";
    gain.gain.value = 0;
    filter.connect(panner).connect(gain).connect(context.destination);
    const graph: SoundGraph = { context, gain, filter, panner, oscillators: [] };

    let source: AudioBufferSourceNode | undefined;
    if (src) {
      setLoading(true);
      try {
        if (!decoded.has(src)) decoded.set(src, fetch(sameOrigin(src)).then((r) => r.arrayBuffer()));
        const buffer = await context.decodeAudioData((await decoded.get(src)!).slice(0));
        source = context.createBufferSource();
        source.buffer = buffer;
        source.loop = true;
        source.connect(filter);
        filter.frequency.value = 6000;
        source.start();
      } catch {
        decoded.delete(src);
        source = undefined;
      }
      setLoading(false);
    }

    if (source) {
      graph.source = source;
      gain.gain.setTargetAtTime(0.6, context.currentTime, 0.6);
    } else {
      // Pas de fichier (ou illisible) : trois oscillateurs, note, quinte et octave.
      filter.frequency.value = 1400;
      graph.oscillators = (["sine", "triangle", "sine"] as OscillatorType[]).map((type, i) => {
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
    }

    sound.current = graph;
    setListening(true);
  }

  return { listening, loading, toggle, steer };
}
