import { LocaleLink } from "@/components/i18n/LocaleLink";
import { ArrowRight } from "lucide-react";
import { MediaImage } from "@/components/ui/MediaImage";
import { getDictionary } from "@/lib/i18n";
import type { Locale } from "@/lib/i18n/locales";
import type { Site } from "@/lib/types";
import { HeaderShell } from "./HeaderShell";
import { LanguageSwitcher } from "./LanguageSwitcher";
import { MainNav } from "./MainNav";
import { MobileMenu } from "./MobileMenu";
import { ThemeToggle } from "./ThemeToggle";

export function SiteHeader({ site, locale }: { site: Site; locale: Locale }) {
  const t = getDictionary(locale).header;
  const links = site.menus.main;
  const navLinks = links.filter((link) => !link.isButton);
  const cta = links.find((link) => link.isButton);

  return (
    <HeaderShell>
      <div className="header-bar relative mx-auto flex h-18 max-w-7xl items-center justify-between gap-6 px-5 sm:px-6 lg:px-8">
        <LocaleLink href="/" className="flex items-center gap-3" aria-label={t.home(site.settings.schoolName)}>
          {site.settings.logo ? (
            <span className="relative block size-12">
              <MediaImage image={site.settings.logo} sizes="48px" priority fit="contain" className="object-left" />
            </span>
          ) : (
            <span className="display text-2xl">EMSI</span>
          )}
          <span className="cartel hidden leading-tight xl:block" aria-hidden>{t.taglineTop}<br />{t.taglineBottom}</span>
        </LocaleLink>

        <MainNav links={navLinks} domains={site.domains} label={t.mainNav} />

        <div className="flex items-center gap-2">
          <LanguageSwitcher className="hidden sm:block" />
          <ThemeToggle className="hidden sm:grid" />
          {cta && (
            <LocaleLink href={cta.url} className="group hidden min-h-11 items-center gap-2 rounded-full bg-brand px-5 text-sm font-semibold text-on-accent shadow-[0_0_40px_-10px_var(--color-brand)] transition hover:brightness-110 sm:inline-flex">
              {cta.label}
              <ArrowRight className="size-4 transition-transform group-hover:translate-x-0.5" aria-hidden />
            </LocaleLink>
          )}
          <MobileMenu links={links} domains={site.domains} />
        </div>
      </div>
    </HeaderShell>
  );
}
