"use client";

import Link from "next/link";
import { useEffect, useRef } from "react";
import { ArrowRight } from "lucide-react";
import type { ScrollTrigger } from "gsap/ScrollTrigger";
import { InView } from "@/components/motion/InView";
import type { RoomSummary } from "@/lib/types";
import { UniverseVisual } from "./UniverseVisual";

const DESKTOP_MOTION =
    "(min-width: 1024px) and (prefers-reduced-motion: no-preference)";

type Props = {
    universes: RoomSummary[];
    eyebrow?: string | null;
    title?: string | null;
    text?: string | null;
};

/**
 * Les univers de l'école. Sur ordinateur (mouvement autorisé), la section s'épingle et les
 * univers défilent horizontalement au fil du défilement ; ailleurs, simple pile verticale.
 */
export function UniversesShowcase({ universes, eyebrow, title, text }: Props) {
    const sectionRef = useRef<HTMLElement>(null);
    const trackRef = useRef<HTMLDivElement>(null);
    const progressRef = useRef<HTMLDivElement>(null);
    const triggerRef = useRef<ScrollTrigger | null>(null);

    // GSAP n'est téléchargé que sur ordinateur, quand le mouvement est autorisé : le mobile n'en paie pas le coût.
    useEffect(() => {
        const media = window.matchMedia(DESKTOP_MOTION);
        let cleanup: (() => void) | undefined;
        let cancelled = false;

        async function setup() {
            cleanup?.();
            cleanup = undefined;
            if (!media.matches) return;
            const [{ default: gsap }, { ScrollTrigger }] = await Promise.all([
                import("gsap"),
                import("gsap/ScrollTrigger"),
            ]);
            if (cancelled || !media.matches) return;
            gsap.registerPlugin(ScrollTrigger);

            const track = trackRef.current!;
            const context = gsap.context(() => {
                track.classList.add("is-horizontal");
                const distance = () =>
                    Math.max(0, track.scrollWidth - window.innerWidth);
                const tween = gsap.to(track, {
                    x: () => -distance(),
                    ease: "none",
                    scrollTrigger: {
                        trigger: sectionRef.current,
                        start: "top top",
                        end: () => `+=${distance()}`,
                        pin: true,
                        scrub: 0.8,
                        invalidateOnRefresh: true,
                        onUpdate: (self) =>
                            progressRef.current?.style.setProperty(
                                "transform",
                                `scaleX(${self.progress})`,
                            ),
                    },
                });
                triggerRef.current = tween.scrollTrigger ?? null;
            }, sectionRef);

            cleanup = () => {
                context.revert();
                triggerRef.current = null;
                track.classList.remove("is-horizontal");
            };
        }

        setup();
        media.addEventListener("change", setup);
        return () => {
            cancelled = true;
            media.removeEventListener("change", setup);
            cleanup?.();
        };
    }, []);

    /** Au clavier, centre à l'écran l'univers qui reçoit le focus pendant le défilement horizontal. */
    function revealPanel(panel: HTMLElement) {
        const trigger = triggerRef.current;
        const track = trackRef.current;
        if (!trigger || !track) return;
        const distance = trigger.end - trigger.start;
        const target =
            panel.offsetLeft - (window.innerWidth - panel.offsetWidth) / 2;
        const progress = Math.min(
            1,
            Math.max(
                0,
                target / Math.max(1, track.scrollWidth - window.innerWidth),
            ),
        );
        window.scrollTo({
            top: trigger.start + distance * progress,
            behavior: "instant",
        });
    }

    // Le <div> englobant appartient à React : l'épinglage GSAP insère son « pin-spacer » à
    // l'intérieur, si bien que React peut toujours retirer la section en changeant de page.
    return (
        <div>
            <section
                ref={sectionRef}
                id="univers"
                className="relative overflow-hidden"
            >
                <div className="flex min-h-svh flex-col justify-center py-20 lg:pb-8 lg:pt-24">
                    <header className="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div className="flex flex-wrap items-end justify-between gap-6">
                            <div className="max-w-4xl">
                                {eyebrow && (
                                    <p className="cartel mb-4 flex items-center gap-3">
                                        <span
                                            className="h-px w-10 bg-brand"
                                            aria-hidden
                                        />
                                        {eyebrow}
                                    </p>
                                )}
                                {title && (
                                    <h2 className="display text-[clamp(2.2rem,4.6vw,4rem)] text-balance">
                                        {title}
                                    </h2>
                                )}
                                {text && (
                                    <p className="mt-4 max-w-2xl text-lg text-ink-muted">
                                        {text}
                                    </p>
                                )}
                            </div>
                            <p className="cartel hidden tabular-nums lg:block">
                                {String(universes.length).padStart(2, "0")}{" "}
                                univers
                            </p>
                        </div>
                        <div
                            className="mt-6 hidden h-px bg-line lg:block"
                            aria-hidden
                        >
                            <div
                                ref={progressRef}
                                className="h-px origin-left scale-x-0 bg-gradient-to-r from-brand via-violet to-hmi"
                            />
                        </div>
                    </header>

                    <div
                        ref={trackRef}
                        className="universe-track mt-10 grid gap-5 px-4 sm:px-6 lg:mt-8 lg:grid-cols-2 lg:px-8"
                    >
                        {universes.map((universe, index) => (
                            <UniversePanel
                                key={universe.id}
                                universe={universe}
                                index={index}
                                onFocus={(event) =>
                                    revealPanel(event.currentTarget)
                                }
                            />
                        ))}
                    </div>
                </div>
            </section>
        </div>
    );
}

function UniversePanel({
    universe,
    index,
    onFocus,
}: {
    universe: RoomSummary;
    index: number;
    onFocus: (event: React.FocusEvent<HTMLAnchorElement>) => void;
}) {
    const tracks = universe.tracks ?? [];
    return (
        <Link
            href={`/univers/${universe.slug}`}
            onFocus={onFocus}
            className="universe-panel group relative grid overflow-hidden rounded-[2rem] border border-line bg-night-2 transition duration-500 hover:border-[var(--accent)] md:grid-cols-[1.05fr_1fr]"
            style={{ ["--accent" as string]: universe.accentColor }}
        >
            <div
                className="absolute inset-x-10 top-0 h-px bg-[var(--accent)] opacity-70 shadow-[0_0_24px_2px_var(--accent)]"
                aria-hidden
            />
            <div className="relative z-10 flex flex-col p-7 sm:p-10">
                <p className="cartel flex items-center gap-3">
                    <span className="tabular-nums text-[var(--accent)]">
                        {String(index + 1).padStart(2, "0")}
                    </span>
                    <span>Univers</span>
                    {universe.isUpcoming && (
                        <span className="rounded-full border border-[var(--accent)] px-2.5 py-0.5 text-[var(--accent)]">
                            Bientôt
                        </span>
                    )}
                </p>
                <h3 className="display mt-5 text-[clamp(2rem,4vw,3.6rem)] text-balance">
                    {universe.name}
                </h3>
                {universe.tagline && (
                    <p className="mt-4 max-w-md text-ink/80">
                        {universe.tagline}
                    </p>
                )}
                {tracks.length > 0 && (
                    <ul
                        className="mt-6 flex flex-wrap gap-2"
                        aria-label="Filières"
                    >
                        {tracks.map((track) => (
                            <li
                                key={track.id}
                                className="rounded-full border border-line bg-night/60 px-3 py-1 text-xs text-ink/85"
                            >
                                {track.shortName}
                            </li>
                        ))}
                    </ul>
                )}
                <span className="mt-auto inline-flex items-center gap-2 pt-8 text-sm font-semibold text-[var(--accent)]">
                    {universe.isUpcoming
                        ? "Découvrir le projet"
                        : "Explorer l'univers"}
                    <ArrowRight
                        className="size-4 transition-transform duration-300 group-hover:translate-x-1.5"
                        aria-hidden
                    />
                </span>
            </div>
            <div
                className="relative min-h-60 border-t border-line md:border-l md:border-t-0"
                style={{
                    background:
                        "radial-gradient(80% 70% at 60% 35%, color-mix(in oklab, var(--accent) 22%, transparent), transparent 70%)",
                }}
            >
                <InView className="absolute inset-4 transition duration-700 group-hover:scale-[1.03]">
                    <UniverseVisual kind={universe.visual} />
                </InView>
            </div>
        </Link>
    );
}
