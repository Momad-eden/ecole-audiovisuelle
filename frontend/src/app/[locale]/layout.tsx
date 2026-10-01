import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { Archivo, Fraunces, Inter, JetBrains_Mono } from "next/font/google";
import { AudioProvider } from "@/components/audio/AudioProvider";
import { PlayerBar } from "@/components/audio/PlayerBar";
import { I18nProvider } from "@/components/i18n/I18nProvider";
import { SiteFooter } from "@/components/layout/SiteFooter";
import { SiteHeader } from "@/components/layout/SiteHeader";
import { WhatsAppButton } from "@/components/layout/WhatsAppButton";
import { themeInitScript } from "@/components/layout/ThemeToggle";
import { api } from "@/lib/api";
import { getDictionary } from "@/lib/i18n";
import { isLocale, LOCALES } from "@/lib/i18n/locales";
import { DEFAULT_SHARE_IMAGE } from "@/lib/metadata";
import { localizedPath } from "@/lib/i18n/locales";
import { siteUrl } from "@/lib/utils";
import "../globals.css";

// Archivo variable : l'axe de largeur (wdth) donne les titres d'affiche étendus.
const archivo = Archivo({ subsets: ["latin"], variable: "--font-archivo", display: "swap", axes: ["wdth"] });
// Police « élégante » proposée dans l'éditeur : téléchargée seulement si un texte l'utilise.
const fraunces = Fraunces({ subsets: ["latin"], variable: "--font-fraunces", display: "swap", preload: false });
const inter = Inter({ subsets: ["latin"], variable: "--font-inter", display: "swap" });
// Police des petits cartels : non préchargée, pour laisser la bande passante aux titres.
const jetbrains = JetBrains_Mono({ subsets: ["latin"], variable: "--font-jetbrains", display: "swap", preload: false });

type Props = { children: React.ReactNode; params: Promise<{ locale: string }> };

/** Les deux langues sont préparées ; les segments imbriqués restent dynamiques (pas de dynamicParams = false ici). */
export function generateStaticParams() {
  return LOCALES.map((locale) => ({ locale }));
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { locale } = await params;
  if (!isLocale(locale)) return {};
  const { settings } = await api.site(locale);
  const title = settings.seoTitle || settings.schoolName;
  const description = settings.seoDescription || settings.description || undefined;

  return {
    metadataBase: new URL(siteUrl),
    title: { default: title, template: `%s — EMSI` },
    description,
    openGraph: { type: "website", locale: getDictionary(locale).meta.ogLocale, siteName: settings.schoolName, title, description, images: [{ ...DEFAULT_SHARE_IMAGE, url: localizedPath(DEFAULT_SHARE_IMAGE.url, locale) }] },
    twitter: { card: "summary_large_image" },
  };
}

/** Layout racine : une seule arborescence pour les deux langues (français réécrit en /fr par src/proxy.ts, anglais sous /en). */
export default async function RootLayout({ children, params }: Props) {
  const { locale } = await params;
  if (!isLocale(locale)) notFound();
  const site = await api.site(locale);
  const { settings } = site;

  const jsonLd = {
    "@context": "https://schema.org",
    "@type": "EducationalOrganization",
    name: settings.schoolName,
    url: siteUrl,
    description: settings.description ?? undefined,
    telephone: settings.phone ?? undefined,
    email: settings.email ?? undefined,
    address: { "@type": "PostalAddress", streetAddress: settings.address ?? undefined, addressLocality: "Dakar", addressCountry: "SN" },
    sameAs: Object.values(settings.social),
  };

  return (
    <html lang={locale} data-theme="dark" suppressHydrationWarning className={`${archivo.variable} ${fraunces.variable} ${inter.variable} ${jetbrains.variable}`}>
      <head>
        <script dangerouslySetInnerHTML={{ __html: themeInitScript }} />
      </head>
      <body className="min-h-dvh bg-night text-ink">
        <I18nProvider locale={locale}>
          <a href="#contenu" className="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[60] focus:rounded-full focus:bg-brand focus:px-5 focus:py-3 focus:text-on-accent">
            {getDictionary(locale).header.skipToContent}
          </a>
          <AudioProvider>
            <SiteHeader site={site} locale={locale} />
            <main id="contenu">{children}</main>
            <SiteFooter site={site} locale={locale} />
            <PlayerBar />
            <WhatsAppButton number={settings.whatsapp} locale={locale} />
          </AudioProvider>
        </I18nProvider>
        <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd).replace(/</g, "\\u003c") }} />
      </body>
    </html>
  );
}
