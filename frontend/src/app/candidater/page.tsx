import type { Metadata } from "next";
import { ApplicationPage } from "@/components/ApplicationPage";

export const metadata: Metadata = { title: "Candidater", description: "Déposez votre candidature en ligne à l'EMSI." };

type Props = { searchParams: Promise<{ formation?: string }> };

export default async function ApplyPage({ searchParams }: Props) {
  return <ApplicationPage audience="school" formation={(await searchParams).formation} />;
}
