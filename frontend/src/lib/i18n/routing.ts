import { DEFAULT_LOCALE, delocalizedPath, isLocale } from "./locales";

export type LocaleRoute = { action: "next" } | { action: "rewrite"; pathname: string } | { action: "redirect"; pathname: string };

/** Ressources servies hors des pages : API, fichiers de Next, médias, fichiers à extension (favicon.ico, robots.txt…). */
const SKIPPED = /^\/(api|_next|storage)(?=$|\/)/;
const FILE = /\/[^/]+\.[a-z0-9]+$/i;

/**
 * Aiguillage d'une adresse (logique pure de src/proxy.ts) :
 * - sans préfixe → réécriture interne vers /fr/… (l'adresse visible ne change pas) ;
 * - /en et /en/… → passent ;
 * - /fr et /fr/… explicites → redirection vers l'adresse sans préfixe (une seule adresse par page française).
 * Jamais de choix selon la langue du navigateur.
 */
export function routeLocale(pathname: string): LocaleRoute {
  if (SKIPPED.test(pathname) || FILE.test(pathname)) return { action: "next" };
  const prefix = pathname.match(/^\/([a-z]{2})(?=$|\/)/)?.[1];
  if (prefix === DEFAULT_LOCALE) return { action: "redirect", pathname: delocalizedPath(pathname) };
  if (isLocale(prefix)) return { action: "next" };
  return { action: "rewrite", pathname: pathname === "/" ? `/${DEFAULT_LOCALE}` : `/${DEFAULT_LOCALE}${pathname}` };
}
