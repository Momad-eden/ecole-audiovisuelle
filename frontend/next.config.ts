import type { NextConfig } from "next";

// Adresse interne de l'API Laravel (côté serveur) et adresse publique des médias.
const apiUrl = new URL(process.env.API_URL ?? "http://127.0.0.1:8000");
const mediaUrl = new URL(process.env.MEDIA_URL ?? apiUrl.origin);

const securityHeaders = [
  { key: "X-Content-Type-Options", value: "nosniff" },
  { key: "Referrer-Policy", value: "strict-origin-when-cross-origin" },
  { key: "X-Frame-Options", value: "SAMEORIGIN" },
  { key: "Permissions-Policy", value: "camera=(), microphone=(), geolocation=()" },
];

const nextConfig: NextConfig = {
  poweredByHeader: false,
  // Le dépôt contient aussi Laravel (et son package.json) : on fixe la racine du projet Next.
  turbopack: { root: import.meta.dirname },
  images: {
    formats: ["image/avif", "image/webp"],
    // En développement, l'API tourne sur 127.0.0.1 ; en production les médias passent par le domaine public.
    dangerouslyAllowLocalIP: process.env.NODE_ENV !== "production",
    remotePatterns: [
      {
        protocol: mediaUrl.protocol.replace(":", "") as "http" | "https",
        hostname: mediaUrl.hostname,
        port: mediaUrl.port,
        pathname: "/storage/**",
      },
      { protocol: "https", hostname: "i.ytimg.com", pathname: "/vi/**" },
    ],
  },
  async redirects() {
    // Le « musée » est devenu les univers (disciplines) et les réalisations des étudiants.
    const rooms: Record<string, string> = { "salle-du-son": "son", "salle-de-la-lumiere": "scene", "salle-de-limage": "image", "salle-du-visuel": "design" };
    const permanent = (source: string, destination: string) => ({ source, destination, permanent: true });
    const french = [
      // Les trois domaines : école (/emsi), Centre culturel Habib Faye et Impact Live Studio (/centre-culturel).
      // /univers mène au bloc des univers de la page EMSI (id="univers").
      permanent("/univers", "/emsi#univers"),
      permanent("/univers/:slug", "/emsi/univers/:slug"),
      permanent("/formations/:path*", "/emsi/formations/:path*"),
      permanent("/realisations/:path*", "/emsi/realisations/:path*"),
      permanent("/professionnels/:path*", "/emsi/professionnels/:path*"),
      permanent("/expositions", "/emsi/realisations"),
      permanent("/expositions/:path*", "/emsi/realisations"),
      // La « Maison Habib Faye » est devenue le « Centre culturel Habib Faye » (mêmes pages, nouvelle adresse).
      permanent("/maison-habib-faye", "/centre-culturel"),
      permanent("/maison-habib-faye/:path*", "/centre-culturel/:path*"),
      permanent("/studio", "/centre-culturel/studio"),
      permanent("/ecole", "/emsi"),
      permanent("/espace-habib-faye", "/centre-culturel"),
      permanent("/agenda", "/centre-culturel/agenda"),
      permanent("/agenda/:slug", "/centre-culturel/agenda/:slug"),
      permanent("/events", "/centre-culturel"),
      permanent("/events/:path*", "/centre-culturel"),
      // Ancienne demande de devis d'Impact Live Events (retiré) : vers la réservation du studio.
      permanent("/demande", "/centre-culturel/studio#reserver"),
      // Le « musée » est devenu les univers (disciplines) et les réalisations des étudiants : cibles directes, sans chaîne.
      permanent("/musee", "/emsi/realisations"),
      permanent("/musee/oeuvres/:slug", "/emsi/realisations/:slug"),
      ...Object.entries(rooms).map(([from, to]) => permanent(`/musee/${from}`, `/emsi/univers/${to}`)),
      permanent("/musee/:slug", "/emsi/univers/:slug"),
    ];
    // Les mêmes anciennes adresses sous /en mènent aux pages anglaises (/en/formations/x → /en/emsi/formations/x).
    return [...french, ...french.map((r) => ({ ...r, source: `/en${r.source}`, destination: `/en${r.destination}` }))];
  },
  async rewrites() {
    // Formulaires et API publics servis sur le même domaine que le site.
    // /storage : médias servis par Laravel (le navigateur peut ainsi lire un son pour en tracer la forme d'onde).
    return [
      { source: "/api/v1/:path*", destination: `${apiUrl.origin}/api/v1/:path*` },
      { source: "/storage/:path*", destination: `${mediaUrl.origin}/storage/:path*` },
    ];
  },
  async headers() {
    return [{ source: "/:path*", headers: securityHeaders }];
  },
};

export default nextConfig;
