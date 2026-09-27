import Link from "next/link";
import { ArrowRight, ChevronDown } from "lucide-react";
import { MediaImage } from "@/components/ui/MediaImage";
import type { Site } from "@/lib/types";
import { HeaderShell } from "./HeaderShell";
import { MobileMenu } from "./MobileMenu";

export function SiteHeader({ site }: { site: Site }) {
  const links = site.menus.main;
  const navLinks = links.filter((link) => !link.isButton);
  const cta = links.find((link) => link.isButton);
  const universes = site.rooms;

  return (
    <HeaderShell>
      <div className="mx-auto flex h-18 max-w-7xl items-center justify-between gap-6 px-4 sm:px-6 lg:px-8">
        <Link href="/" className="flex items-center gap-3" aria-label={`${site.settings.schoolName} — accueil`}>
          {site.settings.logo ? (
            <span className="relative block size-12">
              <MediaImage image={site.settings.logo} sizes="48px" priority fit="contain" className="object-left" />
            </span>
          ) : (
            <span className="display text-2xl">EMSI</span>
          )}
          <span className="cartel hidden leading-tight xl:block" aria-hidden>Son · Image<br />Lumière · Scène</span>
        </Link>

        <nav aria-label="Navigation principale" className="hidden lg:block">
          <ul className="flex items-center gap-1">
            {navLinks.map((link) => {
              const isUniverses = link.url === "/univers" && universes.length > 0;
              return (
                <li key={link.url} className={isUniverses ? "group relative" : undefined}>
                  <Link href={link.url} className="inline-flex items-center gap-1 rounded-full px-4 py-2 text-sm text-ink/80 transition hover:bg-ink/5 hover:text-ink">
                    {link.label}
                    {isUniverses && <ChevronDown className="size-3.5 transition group-hover:rotate-180 group-focus-within:rotate-180" aria-hidden />}
                  </Link>
                  {isUniverses && (
                    <div className="invisible absolute left-1/2 top-full w-[34rem] -translate-x-1/2 pt-3 opacity-0 transition duration-300 group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100">
                      <ul className="grid gap-1 rounded-3xl border border-line bg-night-2/95 p-3 shadow-2xl backdrop-blur-xl sm:grid-cols-2">
                        {universes.map((universe) => (
                          <li key={universe.id}>
                            <Link href={`/univers/${universe.slug}`} className="group/item flex h-full gap-3 rounded-2xl p-3 transition hover:bg-ink/5" style={{ ["--accent" as string]: universe.accentColor }}>
                              <span className="mt-1.5 size-2.5 shrink-0 rounded-full bg-[var(--accent)] shadow-[0_0_14px_var(--accent)]" aria-hidden />
                              <span>
                                <span className="flex items-center gap-2 font-semibold">
                                  {universe.name}
                                  {universe.isUpcoming && <span className="cartel text-[var(--accent)]">Bientôt</span>}
                                </span>
                                {universe.tagline && <span className="mt-1 line-clamp-2 block text-xs text-ink-muted">{universe.tagline}</span>}
                              </span>
                            </Link>
                          </li>
                        ))}
                      </ul>
                    </div>
                  )}
                </li>
              );
            })}
          </ul>
        </nav>

        <div className="flex items-center gap-2">
          {cta && (
            <Link href={cta.url} className="group hidden min-h-11 items-center gap-2 rounded-full bg-brand px-5 text-sm font-semibold text-night shadow-[0_0_40px_-10px_var(--color-brand)] transition hover:brightness-110 sm:inline-flex">
              {cta.label}
              <ArrowRight className="size-4 transition-transform group-hover:translate-x-0.5" aria-hidden />
            </Link>
          )}
          <MobileMenu links={links} universes={universes} />
        </div>
      </div>
    </HeaderShell>
  );
}
