import type { Metadata } from "next";
import Link from "next/link";
import { notFound } from "next/navigation";
import { ArrowLeft } from "lucide-react";
import { AddToQuote } from "@/components/quote/AddToQuote";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { MediaImage } from "@/components/ui/MediaImage";
import { RichText } from "@/components/ui/RichText";
import { api } from "@/lib/api";

type Props = { params: Promise<{ slug: string }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const item = await api.equipmentItem((await params).slug);
  return item ? { title: `${item.name} — location`, description: item.summary ?? undefined } : {};
}

export default async function EquipmentItemPage({ params }: Props) {
  const item = await api.equipmentItem((await params).slug);
  if (!item) notFound();

  return (
    <article className="mx-auto max-w-7xl px-4 pb-24 pt-32 sm:px-6 lg:px-8" style={{ ["--accent" as string]: "var(--color-gold)" }}>
      <Link href={item.category ? `/events/materiel?categorie=${item.category.slug}` : "/events/materiel"} className="cartel inline-flex items-center gap-2 hover:text-ink">
        <ArrowLeft className="size-4" aria-hidden /> Matériel{item.category ? ` · ${item.category.name}` : ""}
      </Link>

      <div className="mt-8 grid gap-12 lg:grid-cols-[1.2fr_1fr]">
        <div className="space-y-4">
          <div className="relative aspect-[4/3] overflow-hidden rounded-[2rem] border border-line bg-night-2">
            {item.image ? <MediaImage image={item.image} sizes="(min-width: 1024px) 55vw, 100vw" priority /> : (
              <div className="absolute inset-0 grid place-items-center" aria-hidden><span className="display text-5xl text-ink/10">{item.brand ?? item.name}</span></div>
            )}
          </div>
          {item.gallery && item.gallery.length > 0 && (
            <ul className="grid grid-cols-3 gap-3">
              {item.gallery.map((image, index) => (
                <li key={index} className="relative aspect-square overflow-hidden rounded-2xl border border-line"><MediaImage image={image} sizes="20vw" /></li>
              ))}
            </ul>
          )}
        </div>

        <div>
          {item.brand && <p className="cartel text-[var(--accent-ink)]">{item.brand}</p>}
          <h1 className="display mt-3 text-[clamp(2.2rem,4.5vw,3.6rem)] text-balance">{item.name}</h1>
          {item.summary && <p className="mt-5 text-lg text-ink/85">{item.summary}</p>}
          <p className="mt-6 inline-flex rounded-full border border-line px-4 py-2 font-mono text-sm">{item.priceLabel}</p>
          <div className="mt-8 flex flex-wrap gap-3">
            <AddToQuote item={{ kind: "equipment", id: item.id, name: item.name }} />
            <ButtonLink href="/demande" variant="secondary">Voir ma demande</ButtonLink>
          </div>

          {item.specs && item.specs.length > 0 && (
            <div className="mt-12">
              <h2 className="cartel mb-4">Caractéristiques</h2>
              <dl className="divide-y divide-line border-y border-line text-sm">
                {item.specs.map((spec) => (
                  <div key={spec.label} className="flex justify-between gap-6 py-3"><dt className="text-ink-muted">{spec.label}</dt><dd className="text-right font-medium">{spec.value}</dd></div>
                ))}
                {item.quantity ? <div className="flex justify-between gap-6 py-3"><dt className="text-ink-muted">Disponibles</dt><dd className="font-medium tabular-nums">{item.quantity}</dd></div> : null}
              </dl>
            </div>
          )}
          {item.description && <RichText html={item.description} className="mt-10" />}
        </div>
      </div>
    </article>
  );
}
