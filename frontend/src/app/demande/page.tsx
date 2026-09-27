import type { Metadata } from "next";
import { LazyBookingForm } from "@/components/forms/LazyBookingForm";
import { PageHeader } from "@/components/ui/PageHeader";
import { Section } from "@/components/ui/Section";

export const metadata: Metadata = {
  title: "Ma demande de devis",
  description: "Demandez un devis à Impact Live Events ou réservez une session à Impact Live Studio.",
  robots: { index: false },
};

export default function QuotePage() {
  return (
    <div style={{ ["--accent" as string]: "var(--color-gold)" }}>
      <PageHeader eyebrow="Impact Live" title="Ma demande" accent="var(--color-gold)"
        text="Vérifiez votre sélection, précisez votre événement : notre équipe vous répond avec une proposition sur mesure." />
      <Section className="pt-0 sm:pt-0">
        <div className="mx-auto max-w-3xl"><LazyBookingForm type="equipment_rental" /></div>
      </Section>
    </div>
  );
}
