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
  async rewrites() {
    // Formulaires et API publics servis sur le même domaine que le site.
    return [{ source: "/api/v1/:path*", destination: `${apiUrl.origin}/api/v1/:path*` }];
  },
  async headers() {
    return [{ source: "/:path*", headers: securityHeaders }];
  },
};

export default nextConfig;
