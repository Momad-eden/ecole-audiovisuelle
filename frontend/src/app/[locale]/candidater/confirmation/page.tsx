import type { Metadata } from "next";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { getDictionary } from "@/lib/i18n";
import type { Locale } from "@/lib/i18n/locales";

type Props = { params: Promise<{ locale: Locale }>; searchParams: Promise<{ ref?: string }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  return { title: getDictionary((await params).locale).meta.confirmation.title, robots: { index: false } };
}

export default async function ConfirmationPage({ params, searchParams }: Props) {
  const { ref } = await searchParams;
  const t = getDictionary((await params).locale).confirmation;

  return (
    <div className="beam mx-auto max-w-3xl px-4 pb-24 pt-40 text-center sm:px-6">
      <p className="cartel">{t.eyebrow}</p>
      <h1 className="display mt-4 text-[clamp(2.2rem,5vw,3.8rem)] text-balance">{t.title}</h1>
      {ref && <p className="mt-6 text-lg">{t.reference}<strong className="font-mono text-brand">{ref}</strong></p>}
      <p className="mx-auto mt-4 max-w-xl text-ink-muted">{t.text}</p>
      <div className="mt-10 flex justify-center gap-3"><ButtonLink href="/emsi/realisations" variant="secondary">{t.seeArtworks}</ButtonLink><ButtonLink href="/">{t.home}</ButtonLink></div>
    </div>
  );
}
