import type { Metadata } from "next";
import { Fraunces, Inter, JetBrains_Mono } from "next/font/google";
import { AudioProvider } from "@/components/audio/AudioProvider";
import { PlayerBar } from "@/components/audio/PlayerBar";
import { SiteFooter } from "@/components/layout/SiteFooter";
import { SiteHeader } from "@/components/layout/SiteHeader";
import { api } from "@/lib/api";
import { siteUrl } from "@/lib/utils";
import "./globals.css";

const fraunces = Fraunces({ subsets: ["latin"], variable: "--font-fraunces", display: "swap", axes: ["opsz"] });
const inter = Inter({ subsets: ["latin"], variable: "--font-inter", display: "swap" });
const jetbrains = JetBrains_Mono({ subsets: ["latin"], variable: "--font-jetbrains", display: "swap" });

export async function generateMetadata(): Promise<Metadata> {
  const { settings } = await api.site();
  const title = settings.seoTitle || settings.schoolName;
  const description = settings.seoDescription || settings.description || undefined;

  return {
    metadataBase: new URL(siteUrl),
    title: { default: title, template: `%s — EMSI` },
    description,
    openGraph: { type: "website", locale: "fr_SN", siteName: settings.schoolName, title, description },
    twitter: { card: "summary_large_image" },
  };
}

export default async function RootLayout({ children }: Readonly<{ children: React.ReactNode }>) {
  const site = await api.site();
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
    <html lang="fr" className={`${fraunces.variable} ${inter.variable} ${jetbrains.variable}`}>
      <body className="min-h-dvh bg-night text-ink">
        <a href="#contenu" className="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[60] focus:rounded-full focus:bg-amber focus:px-5 focus:py-3 focus:text-night">
          Aller au contenu
        </a>
        <AudioProvider>
          <SiteHeader site={site} />
          <main id="contenu" className="pb-24">{children}</main>
          <SiteFooter site={site} />
          <PlayerBar />
        </AudioProvider>
        <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd).replace(/</g, "\\u003c") }} />
      </body>
    </html>
  );
}
