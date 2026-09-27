import Link from "next/link";
import { MediaImage } from "@/components/ui/MediaImage";
import type { Site } from "@/lib/types";

const SOCIAL_LABELS: Record<string, string> = {
  facebook: "Facebook", instagram: "Instagram", youtube: "YouTube", tiktok: "TikTok", linkedin: "LinkedIn", twitter: "X",
};

export function SiteFooter({ site }: { site: Site }) {
  const { settings, menus } = site;
  const whatsapp = settings.whatsapp?.replace(/[^0-9]/g, "");
  const explore = [...menus.main.filter((l) => !l.isButton), ...menus.footer];

  return (
    <footer className="relative overflow-hidden border-t border-line bg-night-2">
      <div className="mx-auto grid max-w-7xl gap-12 px-4 pb-10 pt-20 sm:px-6 md:grid-cols-2 lg:grid-cols-[1.4fr_1fr_1fr_1fr] lg:px-8">
        <div>
          {settings.logo && (
            <span className="relative mb-5 block size-20">
              <MediaImage image={settings.logo} sizes="80px" fit="contain" className="object-left" />
            </span>
          )}
          <p className="display text-xl leading-tight">{settings.schoolName}</p>
          {settings.description && <p className="mt-4 max-w-sm text-sm text-ink-muted">{settings.description}</p>}
          {Object.keys(settings.social).length > 0 && (
            <ul className="mt-6 flex flex-wrap gap-2 text-sm">
              {Object.entries(settings.social).map(([network, url]) => (
                <li key={network}>
                  <a href={url} target="_blank" rel="noopener noreferrer" className="inline-flex min-h-10 items-center rounded-full border border-line px-4 text-ink/80 transition hover:border-brand hover:text-brand">
                    {SOCIAL_LABELS[network] ?? network}
                  </a>
                </li>
              ))}
            </ul>
          )}
        </div>

        <nav aria-label="Liens du pied de page">
          <p className="cartel mb-5">Explorer</p>
          <ul className="space-y-1 text-sm">
            {explore.map((link) => (
              <li key={`${link.url}-${link.label}`}><Link href={link.url} className="inline-flex min-h-8 items-center text-ink/80 transition hover:text-brand">{link.label}</Link></li>
            ))}
          </ul>
        </nav>

        {site.rooms.length > 0 && (
          <nav aria-label="Les univers">
            <p className="cartel mb-5">Univers</p>
            <ul className="space-y-1 text-sm">
              {site.rooms.map((universe) => (
                <li key={universe.id}>
                  <Link href={`/univers/${universe.slug}`} className="inline-flex min-h-8 items-center gap-2 text-ink/80 transition hover:text-ink" style={{ ["--accent" as string]: universe.accentColor }}>
                    <span className="size-1.5 rounded-full bg-[var(--accent-ink)]" aria-hidden />
                    {universe.name}
                  </Link>
                </li>
              ))}
            </ul>
          </nav>
        )}

        <div>
          <p className="cartel mb-5">Nous joindre</p>
          <address className="space-y-3 text-sm not-italic text-ink/80">
            {site.places.length > 0 ? (
              site.places.map((place) => (
                <p key={place.id}><span className="block font-medium text-ink">{place.name}</span>{[place.address, place.city].filter(Boolean).join(", ")}</p>
              ))
            ) : (
              <p>{settings.address || "Grand Théâtre National Doudou Ndiaye Coumba Rose, Dakar"}</p>
            )}
            {settings.phone && <p><a href={`tel:${settings.phone.replace(/[^0-9+]/g, "")}`} className="hover:text-brand">{settings.phone}</a></p>}
            {settings.email && <p><a href={`mailto:${settings.email}`} className="hover:text-brand">{settings.email}</a></p>}
            {whatsapp && <p><a href={`https://wa.me/${whatsapp}`} target="_blank" rel="noopener noreferrer" className="hover:text-brand">Écrire sur WhatsApp</a></p>}
            {settings.openingHours && <p className="text-ink-muted">{settings.openingHours}</p>}
          </address>
        </div>
      </div>

      <p className="display text-outline pointer-events-none select-none px-4 text-center text-[clamp(6rem,26vw,22rem)] leading-[0.8]" aria-hidden>EMSI</p>

      <div className="border-t border-line">
        <div className="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-4 py-6 text-xs text-ink-muted sm:px-6 lg:px-8">
          <p>© {new Date().getFullYear()} {settings.schoolName}</p>
          <ul className="flex flex-wrap gap-4">
            {menus.legal.map((link) => (
              <li key={link.url}><Link href={link.url} className="inline-flex min-h-8 items-center hover:text-ink">{link.label}</Link></li>
            ))}
          </ul>
        </div>
      </div>
    </footer>
  );
}
