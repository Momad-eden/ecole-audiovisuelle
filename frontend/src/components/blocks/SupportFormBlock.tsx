import { HandHeart } from "lucide-react";
import { LazySupportForm } from "@/components/forms/LazySupportForm";
import { Section } from "@/components/ui/Section";
import { frenchSpacing } from "@/lib/utils";
import type { SupportFormData } from "./types";

/** « Nous soutenir » : texte d'appel puis formulaire (partenariat, mécénat, don, autre). */
export function SupportFormBlock({ data, first }: { data: SupportFormData; first: boolean }) {
  const Heading = first ? "h1" : "h2";
  return (
    <Section className={first ? "pt-16 sm:pt-20" : undefined}>
      <div className="grid gap-12 lg:grid-cols-[1fr_1.35fr]">
        <div>
          <p className="cartel mb-5 flex items-center gap-3" style={{ color: "var(--accent-ink)" }}>
            <HandHeart className="size-4" aria-hidden />Partenariat · mécénat · don
          </p>
          <Heading className="display text-[clamp(2.4rem,5.5vw,4.4rem)] text-balance">{frenchSpacing(data.title || "Nous soutenir")}</Heading>
          {data.text && <p className="mt-5 text-lg text-ink-muted">{frenchSpacing(data.text)}</p>}
        </div>
        <LazySupportForm />
      </div>
    </Section>
  );
}
