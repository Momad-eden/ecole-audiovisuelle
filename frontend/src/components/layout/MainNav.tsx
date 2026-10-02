"use client";

import { usePathname } from "next/navigation";
import { LocaleLink } from "@/components/i18n/LocaleLink";
import { domainSection } from "@/lib/domains";
import { delocalizedPath } from "@/lib/i18n/locales";
import type { Domains, MenuLink } from "@/lib/types";
import { cn } from "@/lib/utils";
import { menuDomain } from "./menu-icons";
import { NavDropdown } from "./NavDropdown";

/** Couleur d'une entrée du menu : celle de son domaine, sinon la couleur de la marque. */
export function menuColor(url: string, domains?: Domains): string | undefined {
  const domain = menuDomain(url);
  return domain === "general" ? undefined : domains?.[domain]?.color;
}

/**
 * Navigation principale (ordinateur) : une pilule de verre, la rubrique où l'on se trouve marquée
 * d'un point de sa couleur, et des sous-menus riches (icône, description).
 */
export function MainNav({ links, domains, label }: { links: MenuLink[]; domains?: Domains; label: string }) {
  const path = delocalizedPath(usePathname() || "/");
  const clean = path.length > 1 ? path.replace(/\/+$/, "") : path;
  const section = domainSection(links, clean);

  return (
    <nav aria-label={label} className="hidden lg:block">
      {/* Pas de backdrop-filter ici : il ferait de la pilule le repère des méga-menus (centrés alors sous elle, hors écran). */}
      <ul className="main-nav-pill flex items-center gap-0.5 rounded-full border border-line bg-night/60 p-1 shadow-[0_8px_32px_-12px_rgb(0_0_0/0.45)]">
        {links.map((link) => {
          const color = menuColor(link.url, domains);
          if (link.children?.length) {
            return <NavDropdown key={link.url + link.label} link={link} color={color} active={section?.parent === link} currentUrl={section?.parent === link ? section.current?.url : undefined} domains={domains} />;
          }
          const active = clean === link.url || section?.parent === link;
          return (
            <li key={link.url + link.label}>
              <LocaleLink
                href={link.url}
                aria-current={active ? "page" : undefined}
                className={cn("nav-pill", active && "nav-pill-active")}
              >
                {active && <span className="size-1.5 rounded-full bg-[var(--accent-ink)]" style={color ? { background: color } : undefined} aria-hidden />}
                {link.label}
              </LocaleLink>
            </li>
          );
        })}
      </ul>
    </nav>
  );
}
