import type { MenuChild, MenuLink, Site } from "./types";

export type DomainSection = { parent: MenuLink; current?: MenuChild };

const matches = (url: string, path: string) => url !== "/" && (path === url || path.startsWith(`${url}/`));

/**
 * Section du menu à laquelle appartient un chemin : l'entrée dont un enfant (ou elle-même)
 * correspond, en exact d'abord, sinon au plus long préfixe. Null hors des sections.
 */
export function domainSection(menus: Site["menus"]["main"], path: string): DomainSection | null {
  const clean = path.length > 1 ? path.replace(/\/+$/, "") : path;
  let best: (DomainSection & { length: number }) | null = null;

  for (const parent of menus) {
    if (parent.isButton) continue;
    const candidates: { url: string; child?: MenuChild }[] = [{ url: parent.url }, ...(parent.children ?? []).map((child) => ({ url: child.url, child }))];
    for (const { url, child } of candidates) {
      if (!matches(url, clean)) continue;
      // Le plus long préfixe l'emporte ; à égalité, l'enfant passe avant son parent.
      if (best && (url.length < best.length || (url.length === best.length && (!child || best.current)))) continue;
      best = { parent, current: child, length: url.length };
    }
  }

  return best ? { parent: best.parent, current: best.current } : null;
}
