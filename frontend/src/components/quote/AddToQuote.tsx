"use client";

import Link from "next/link";
import { Check, Plus } from "lucide-react";
import { cn } from "@/lib/utils";
import { useQuote, type QuoteItem } from "./useQuote";

/** Ajoute un matériel, un pack ou un service à « Ma demande ». */
export function AddToQuote({ item, className, compact }: { item: Omit<QuoteItem, "quantity">; className?: string; compact?: boolean }) {
  const quote = useQuote();
  const added = quote.has(item);

  if (added) {
    return (
      <Link href="/demande" className={cn("inline-flex min-h-11 items-center gap-2 rounded-full border border-[var(--accent-ink)] px-5 text-sm font-semibold text-[var(--accent-ink)]", className)}>
        <Check className="size-4" aria-hidden /> {compact ? "Ajouté" : "Dans ma demande"}
      </Link>
    );
  }

  return (
    <button type="button" onClick={() => quote.add(item)} className={cn("inline-flex min-h-11 items-center gap-2 rounded-full bg-[var(--accent-ink)] px-5 text-sm font-semibold text-on-accent transition hover:brightness-110", className)}>
      <Plus className="size-4" aria-hidden /> {compact ? "Ajouter" : "Ajouter à ma demande"}
      <span className="sr-only">: {item.name}</span>
    </button>
  );
}
