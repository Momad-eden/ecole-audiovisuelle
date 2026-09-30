import { NextResponse, type NextRequest } from "next/server";
import { routeLocale } from "@/lib/i18n/routing";

/**
 * Aiguillage des langues (convention « proxy » de Next 16, ex-middleware) :
 * adresses sans préfixe → pages françaises (/fr réécrit en interne), /en/… → anglais, /fr/… → 308 vers l'adresse sans préfixe.
 * La logique pure est dans src/lib/i18n/routing.ts (testée).
 */
export function proxy(request: NextRequest) {
  const route = routeLocale(request.nextUrl.pathname);
  if (route.action === "next") return NextResponse.next();

  const url = request.nextUrl.clone();
  url.pathname = route.pathname;
  return route.action === "redirect" ? NextResponse.redirect(url, 308) : NextResponse.rewrite(url);
}

export const config = {
  // Les fichiers de Next ne passent jamais par l'aiguillage ; le reste est trié par routeLocale.
  matcher: ["/((?!_next/).*)"],
};
