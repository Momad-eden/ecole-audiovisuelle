import { Clock, Mail, MapPin, MessageCircle, Phone } from "lucide-react";
import { LazyContactForm } from "@/components/forms/LazyContactForm";
import { Section } from "@/components/ui/Section";
import { frenchSpacing } from "@/lib/utils";
import type { ContactData } from "./types";

/** Contact : actions directes (appeler, WhatsApp, e-mail) puis formulaire. */
export function ContactBlock({ data }: { data: ContactData }) {
  const s = data.settings ?? {};
  const whatsapp = s.whatsapp?.replace(/[^0-9]/g, "");
  const phone = s.phone?.replace(/[^0-9+]/g, "");
  const action = "flex min-h-16 items-center gap-4 rounded-2xl border border-line bg-night-2 px-5 transition hover:border-[var(--accent)]";

  return (
    <Section className="pt-16 sm:pt-20">
      <div className="grid gap-12 lg:grid-cols-[1fr_1.35fr]">
        <div>
          <p className="cartel mb-5 flex items-center gap-3 text-[var(--accent-ink)]"><span className="h-px w-10 bg-[var(--accent-ink)]" aria-hidden />Contact</p>
          <h1 className="display text-[clamp(2.4rem,5.5vw,4.4rem)] text-balance">{frenchSpacing(data.title ?? "Nous contacter")}</h1>
          {data.text && <p className="mt-5 text-lg text-ink-muted">{data.text}</p>}

          <ul className="mt-10 grid gap-3">
            {whatsapp && (
              <li><a href={`https://wa.me/${whatsapp}`} target="_blank" rel="noopener noreferrer" className={action}>
                <MessageCircle className="size-6 text-[#25d366]" aria-hidden />
                <span><span className="block font-semibold">Écrire sur WhatsApp</span><span className="text-sm text-ink-muted">Réponse la plus rapide</span></span>
                <span className="sr-only"> (nouvel onglet)</span>
              </a></li>
            )}
            {phone && (
              <li><a href={`tel:${phone}`} className={action}>
                <Phone className="size-6 text-[var(--accent-ink)]" aria-hidden />
                <span><span className="block font-semibold">Appeler</span><span className="text-sm text-ink-muted">{s.phone}</span></span>
              </a></li>
            )}
            {s.email && (
              <li><a href={`mailto:${s.email}`} className={action}>
                <Mail className="size-6 text-[var(--accent-ink)]" aria-hidden />
                <span><span className="block font-semibold">Envoyer un e-mail</span><span className="text-sm text-ink-muted">{s.email}</span></span>
              </a></li>
            )}
          </ul>

          <div className="mt-8 space-y-3 text-sm text-ink/85">
            {s.address && <p className="flex gap-3"><MapPin className="mt-0.5 size-4 shrink-0 text-ink-muted" aria-hidden />{s.address}</p>}
            {s.openingHours && <p className="flex gap-3"><Clock className="mt-0.5 size-4 shrink-0 text-ink-muted" aria-hidden />{s.openingHours}</p>}
            {s.mapUrl && <p><a href={s.mapUrl} target="_blank" rel="noopener noreferrer" className="font-semibold text-[var(--accent-ink)] underline underline-offset-4">Voir le plan d&apos;accès<span className="sr-only"> (nouvel onglet)</span></a></p>}
          </div>
        </div>
        <LazyContactForm />
      </div>
    </Section>
  );
}
