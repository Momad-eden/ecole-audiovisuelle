import { LazyContactForm } from "@/components/forms/LazyContactForm";
import { Section } from "@/components/ui/Section";
import type { ContactData } from "./types";

export function ContactBlock({ data }: { data: ContactData }) {
  const s = data.settings ?? {};
  const whatsapp = s.whatsapp?.replace(/[^0-9]/g, "");

  return (
    <Section>
      <div className="grid gap-12 lg:grid-cols-5">
        <div className="lg:col-span-2">
          <h1 className="font-display text-4xl sm:text-5xl">{data.title ?? "Nous contacter"}</h1>
          {data.text && <p className="mt-4 text-lg text-ink-muted">{data.text}</p>}
          <address className="mt-10 space-y-4 not-italic">
            <p><span className="cartel block">Adresse</span>{s.address || "Dakar, Sénégal"}</p>
            {s.phone && <p><span className="cartel block">Téléphone</span><a href={`tel:${s.phone.replace(/[^0-9+]/g, "")}`} className="hover:text-brand">{s.phone}</a></p>}
            {s.email && <p><span className="cartel block">E-mail</span><a href={`mailto:${s.email}`} className="hover:text-brand">{s.email}</a></p>}
            {whatsapp && <p><span className="cartel block">WhatsApp</span><a href={`https://wa.me/${whatsapp}`} target="_blank" rel="noopener noreferrer" className="hover:text-brand">Écrire sur WhatsApp</a></p>}
            {s.openingHours && <p><span className="cartel block">Horaires</span>{s.openingHours}</p>}
            {s.mapUrl && <p><a href={s.mapUrl} target="_blank" rel="noopener noreferrer" className="text-brand underline underline-offset-4">Voir le plan d&apos;accès</a></p>}
          </address>
        </div>
        <div className="lg:col-span-3"><LazyContactForm /></div>
      </div>
    </Section>
  );
}
