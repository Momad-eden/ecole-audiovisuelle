"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { ClipboardList } from "lucide-react";
import { useQuote } from "./useQuote";

/** Pastille flottante « Ma demande » dès qu'un élément est sélectionné. */
export function QuoteBar() {
  const { count } = useQuote();
  const pathname = usePathname();
  if (count === 0 || pathname === "/demande") return null;

  return (
    <Link
      href="/demande"
      className="fixed bottom-24 left-4 z-40 inline-flex min-h-12 items-center gap-3 rounded-full bg-gold px-5 font-semibold text-on-accent shadow-[0_10px_40px_-10px_var(--color-gold)] transition hover:brightness-110 sm:bottom-6"
      style={{ ["--accent" as string]: "var(--color-gold)" }}
    >
      <ClipboardList className="size-5" aria-hidden />
      Ma demande <span className="rounded-full bg-night/15 px-2 py-0.5 text-sm tabular-nums">{count}</span>
    </Link>
  );
}
