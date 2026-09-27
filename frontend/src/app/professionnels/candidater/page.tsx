import type { Metadata } from "next";
import { ApplicationPage } from "@/components/ApplicationPage";

export const metadata: Metadata = { title: "Candidature professionnelle", description: "Candidater au programme EMSI × Grand Théâtre (Volet 1, certification de niveau BTS par la VAE)." };

type Props = { searchParams: Promise<{ formation?: string }> };

export default async function ProfessionalApplyPage({ searchParams }: Props) {
  return <div style={{ ["--accent" as string]: "var(--color-hmi)" }}><ApplicationPage audience="professional" formation={(await searchParams).formation} /></div>;
}
