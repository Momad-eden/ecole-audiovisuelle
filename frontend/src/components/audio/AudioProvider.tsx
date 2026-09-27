"use client";

import { createContext, useCallback, useContext, useEffect, useMemo, useRef, useState, type ReactNode } from "react";

export type Track = {
  src: string;
  title: string;
  subtitle?: string | null;
  href?: string | null;
  peaks?: number[] | null;
  accent?: string | null;
};

type AudioState = {
  track: Track | null;
  playing: boolean;
  currentTime: number;
  duration: number;
  play: (track: Track) => void;
  toggle: () => void;
  seek: (ratio: number) => void;
  close: () => void;
};

const AudioContext = createContext<AudioState | null>(null);

/** Lecteur unique pour tout le site : la lecture continue quand on change de page. */
export function AudioProvider({ children }: { children: ReactNode }) {
  const audioRef = useRef<HTMLAudioElement | null>(null);
  const [track, setTrack] = useState<Track | null>(null);
  const [playing, setPlaying] = useState(false);
  const [currentTime, setCurrentTime] = useState(0);
  const [duration, setDuration] = useState(0);

  useEffect(() => {
    const audio = new Audio();
    audio.preload = "metadata";
    audioRef.current = audio;
    const onTime = () => setCurrentTime(audio.currentTime);
    const onMeta = () => setDuration(Number.isFinite(audio.duration) ? audio.duration : 0);
    const onPlay = () => setPlaying(true);
    const onPause = () => setPlaying(false);
    audio.addEventListener("timeupdate", onTime);
    audio.addEventListener("loadedmetadata", onMeta);
    audio.addEventListener("play", onPlay);
    audio.addEventListener("pause", onPause);
    audio.addEventListener("ended", onPause);
    return () => {
      audio.pause();
      audio.removeEventListener("timeupdate", onTime);
      audio.removeEventListener("loadedmetadata", onMeta);
      audio.removeEventListener("play", onPlay);
      audio.removeEventListener("pause", onPause);
      audio.removeEventListener("ended", onPause);
    };
  }, []);

  const play = useCallback((next: Track) => {
    const audio = audioRef.current;
    if (!audio) return;
    if (audio.src !== next.src) {
      audio.src = next.src;
      setCurrentTime(0);
      setDuration(0);
    }
    setTrack(next);
    void audio.play();
  }, []);

  const toggle = useCallback(() => {
    const audio = audioRef.current;
    if (!audio || !audio.src) return;
    if (audio.paused) void audio.play();
    else audio.pause();
  }, []);

  const seek = useCallback((ratio: number) => {
    const audio = audioRef.current;
    if (!audio || !audio.duration) return;
    audio.currentTime = Math.min(Math.max(ratio, 0), 1) * audio.duration;
  }, []);

  const close = useCallback(() => {
    audioRef.current?.pause();
    setTrack(null);
  }, []);

  const value = useMemo(() => ({ track, playing, currentTime, duration, play, toggle, seek, close }), [track, playing, currentTime, duration, play, toggle, seek, close]);

  return <AudioContext.Provider value={value}>{children}</AudioContext.Provider>;
}

export function useAudio(): AudioState {
  const context = useContext(AudioContext);
  if (!context) throw new Error("useAudio doit être utilisé dans <AudioProvider>.");
  return context;
}
