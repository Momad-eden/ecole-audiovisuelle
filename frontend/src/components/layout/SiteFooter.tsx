import Link from "next/link";
import type { Site } from "@/lib/types";

const SOCIAL_LABELS: Record<string, string> = {
  facebook: "Facebook", instagram: "Instagram", youtube: "YouTube", tiktok: "TikTok", linkedin: "LinkedIn", twitter: "X",
};

export function SiteFooter({ site }: { site: Site }) {
  const { settings, menus } = site;
  const whatsapp = settings.whatsapp?.replace(/[^0-9]/g, "");

  return (
    <footer className="mt-24 border-t border-line bg-night-2">
      <div className="mx-auto grid max-w-7xl gap-12 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
        <div>
          <p className="font-display text-2xl">{settings.schoolName}</p>
          {settings.description && <p className="mt-3 max-w-sm text-sm text-ink-muted">{settings.description}</p>}
          {Object.keys(settings.social).length > 0 && (
            <ul className="mt-6 flex flex-wrap gap-3 text-sm">
              {Object.entries(settings.social).map(([network, url]) => (
                <li key={network}>
                  <a href={url} target="_blank" rel="noopener noreferrer" className="text-ink-muted hover:text-amber">
                    {SOCIAL_LABELS[network] ?? network}
                  </a>
                </li>
              ))}
            </ul>
          )}
        </div>

        <nav aria-label="Liens du pied de page">
          <p className="cartel mb-4">Explorer</p>
          <ul className="space-y-2 text-sm">
            {[...menus.main.filter((l) => !l.isButton && (site.hasSchoolPrograms || l.url !== "/formations")), ...menus.footer].map((link) => (
              <li key={`${link.url}-${link.label}`}>
                <Link href={link.url} className="text-ink/80 hover:text-amber">{link.label}</Link>
              </li>
            ))}
          </ul>
        </nav>

        <div>
          <p className="cartel mb-4">Nous joindre</p>
          <address className="space-y-2 text-sm not-italic text-ink/80">
            <p>{settings.address || "Dakar, Sénégal"}</p>
            {settings.phone && <p><a href={`tel:${settings.phone.replace(/[^0-9+]/g, "")}`} className="hover:text-amber">{settings.phone}</a></p>}
            {settings.email && <p><a href={`mailto:${settings.email}`} className="hover:text-amber">{settings.email}</a></p>}
            {whatsapp && <p><a href={`https://wa.me/${whatsapp}`} target="_blank" rel="noopener noreferrer" className="hover:text-amber">Écrire sur WhatsApp</a></p>}
            {settings.openingHours && <p className="text-ink-muted">{settings.openingHours}</p>}
          </address>
        </div>
      </div>
      <div className="border-t border-line">
        <div className="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-4 py-6 text-xs text-ink-muted sm:px-6 lg:px-8">
          <p>© {new Date().getFullYear()} {settings.schoolName}</p>
          <ul className="flex flex-wrap gap-4">
            {menus.legal.map((link) => (
              <li key={link.url}><Link href={link.url} className="hover:text-ink">{link.label}</Link></li>
            ))}
          </ul>
        </div>
      </div>
    </footer>
  );
}
