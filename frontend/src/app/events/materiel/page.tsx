import type { Metadata } from "next";
import Link from "next/link";
import { EquipmentCard } from "@/components/blocks/ImpactBlocks";
import { ButtonLink } from "@/components/ui/ButtonLink";
import { PageHeader } from "@/components/ui/PageHeader";
import { Section } from "@/components/ui/Section";
import { api } from "@/lib/api";
import { cn } from "@/lib/utils";

export const metadata: Metadata = {
  title: "Location de matériel",
  description: "Sonorisation, lumière, podiums : le matériel de spectacle d'Impact Live Events à louer, avec ou sans techniciens, à Saint-Louis et partout au Sénégal.",
};

type Props = { searchParams: Promise<{ categorie?: string }> };

export default async function EquipmentCatalogPage({ searchParams }: Props) {
  const { categorie } = await searchParams;
  const [categories, items] = await Promise.all([
    api.equipmentCategories(),
    api.equipment(`usage=rental${categorie ? `&category=${encodeURIComponent(categorie)}` : ""}`),
  ]);
  const active = categories.find((c) => c.slug === categorie);

  return (
    <div style={{ ["--accent" as string]: "var(--color-gold)" }}>
      <PageHeader eyebrow="Impact Live Events · Location" title={active ? active.name : "Le matériel à louer"} accent="var(--color-gold)"
        text={active?.summary ?? "Choisissez votre matériel, ajoutez-le à votre demande : nous vous répondons avec un devis, livraison, installation et techniciens compris si vous le souhaitez."} />

      <Section className="pt-0 sm:pt-0">
        {categories.length > 0 && (
          <nav aria-label="Catégories de matériel" className="mb-12">
            <ul className="flex flex-wrap gap-2">
              {[{ slug: undefined, name: "Tout", itemsCount: undefined }, ...categories].map((category) => {
                const selected = category.slug === active?.slug;
                return (
                  <li key={category.slug ?? "all"}>
                    <Link href={category.slug ? `/events/materiel?categorie=${category.slug}` : "/events/materiel"} aria-current={selected ? "page" : undefined}
                      className={cn("inline-flex min-h-11 items-center gap-2 rounded-full border px-5 text-sm transition", selected ? "border-[var(--accent-ink)] bg-[var(--accent-ink)] text-on-accent" : "border-line hover:border-[var(--accent)]")}>
                      {category.name}{category.itemsCount !== undefined && <span className="tabular-nums opacity-70">{category.itemsCount}</span>}
                    </Link>
                  </li>
                );
              })}
            </ul>
          </nav>
        )}

        {items.length > 0 ? (
          <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            {items.map((item) => <EquipmentCard key={item.id} item={item} />)}
          </div>
        ) : (
          <div className="rounded-3xl border border-dashed border-line p-10 text-center">
            <p className="text-lg text-ink-muted">Le catalogue en ligne est en cours de préparation. Décrivez-nous votre événement : nous vous proposons le matériel adapté.</p>
            <div className="mt-6 flex justify-center"><ButtonLink href="/demande">Demander un devis</ButtonLink></div>
          </div>
        )}
      </Section>
    </div>
  );
}
