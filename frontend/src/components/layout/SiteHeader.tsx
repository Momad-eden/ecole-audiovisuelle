import Link from "next/link";
import type { Site } from "@/lib/types";
import { MobileMenu } from "./MobileMenu";

export function visibleMainLinks(site: Site) {
  // Tant qu'aucune formation de l'école n'est publiée, l'entrée « Formations » est masquée.
  return site.menus.main.filter((link) => site.hasSchoolPrograms || link.url !== "/formations");
}

export function SiteHeader({ site }: { site: Site }) {
  const links = visibleMainLinks(site);
  const navLinks = links.filter((link) => !link.isButton);
  const cta = links.find((link) => link.isButton);

  return (
    <header className="sticky top-0 z-40 border-b border-line bg-night/85 backdrop-blur">
      <div className="mx-auto flex h-16 max-w-7xl items-center justify-between gap-6 px-4 sm:px-6 lg:px-8">
        <Link href="/" className="flex items-baseline gap-2" aria-label={`${site.settings.schoolName} — accueil`}>
          <span className="font-display text-2xl font-semibold tracking-tight">EMSI</span>
          <span className="cartel hidden sm:inline">Son · Lumière · Image</span>
        </Link>

        <nav aria-label="Navigation principale" className="hidden lg:block">
          <ul className="flex items-center gap-1">
            {navLinks.map((link) => (
              <li key={link.url}>
                <Link href={link.url} className="rounded-full px-4 py-2 text-sm text-ink/80 transition hover:bg-ink/5 hover:text-ink">
                  {link.label}
                </Link>
              </li>
            ))}
          </ul>
        </nav>

        <div className="flex items-center gap-2">
          {cta && (
            <Link href={cta.url} className="hidden min-h-11 items-center rounded-full bg-amber px-5 text-sm font-semibold text-night hover:brightness-110 sm:inline-flex">
              {cta.label}
            </Link>
          )}
          <MobileMenu links={links} />
        </div>
      </div>
    </header>
  );
}
