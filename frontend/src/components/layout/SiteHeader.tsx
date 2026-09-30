import { LocaleLink } from "@/components/i18n/LocaleLink";
import { ArrowRight } from "lucide-react";
import { MediaImage } from "@/components/ui/MediaImage";
import type { Site } from "@/lib/types";
import { HeaderShell } from "./HeaderShell";
import { MobileMenu } from "./MobileMenu";
import { NavDropdown } from "./NavDropdown";
import { ThemeToggle } from "./ThemeToggle";

export function SiteHeader({ site }: { site: Site }) {
  const links = site.menus.main;
  const navLinks = links.filter((link) => !link.isButton);
  const cta = links.find((link) => link.isButton);

  return (
    <HeaderShell>
      <div className="mx-auto flex h-18 max-w-7xl items-center justify-between gap-6 px-4 sm:px-6 lg:px-8">
        <LocaleLink href="/" className="flex items-center gap-3" aria-label={`${site.settings.schoolName} — accueil`}>
          {site.settings.logo ? (
            <span className="relative block size-12">
              <MediaImage image={site.settings.logo} sizes="48px" priority fit="contain" className="object-left" />
            </span>
          ) : (
            <span className="display text-2xl">EMSI</span>
          )}
          <span className="cartel hidden leading-tight xl:block" aria-hidden>Son · Image<br />Lumière · Scène</span>
        </LocaleLink>

        <nav aria-label="Navigation principale" className="hidden lg:block">
          <ul className="flex items-center gap-1">
            {navLinks.map((link) =>
              link.children?.length ? (
                <NavDropdown key={link.url + link.label} link={link} />
              ) : (
                <li key={link.url + link.label}>
                  <LocaleLink href={link.url} className="inline-flex items-center rounded-full px-4 py-2 text-sm text-ink/80 transition hover:bg-ink/5 hover:text-ink">
                    {link.label}
                  </LocaleLink>
                </li>
              ),
            )}
          </ul>
        </nav>

        <div className="flex items-center gap-2">
          <ThemeToggle className="hidden sm:grid" />
          {cta && (
            <LocaleLink href={cta.url} className="group hidden min-h-11 items-center gap-2 rounded-full bg-brand px-5 text-sm font-semibold text-on-accent shadow-[0_0_40px_-10px_var(--color-brand)] transition hover:brightness-110 sm:inline-flex">
              {cta.label}
              <ArrowRight className="size-4 transition-transform group-hover:translate-x-0.5" aria-hidden />
            </LocaleLink>
          )}
          <MobileMenu links={links} />
        </div>
      </div>
    </HeaderShell>
  );
}
