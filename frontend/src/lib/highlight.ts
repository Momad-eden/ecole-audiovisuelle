/** Espaces considérés comme identiques : normal, insécable, fine insécable (voir frenchSpacing). */
const SPACE = "[ \\u00a0\\u202f]";

/**
 * Coupe le titre autour du mot à mettre en couleur choisi dans l'admin : [avant, mot, après],
 * avec les caractères du titre d'origine. Insensible à la casse et au type d'espace ;
 * seule la première occurrence compte. null si le mot est vide ou absent du titre.
 */
export function splitHighlight(title: string, highlight?: string | null): [before: string, match: string, after: string] | null {
  const wanted = highlight?.trim();
  if (!wanted) return null;

  const pattern = wanted
    .split(/[   ]+/)
    .map((part) => part.replace(/[.*+?^${}()|[\]\\]/g, "\\$&"))
    .join(`${SPACE}+`);
  const found = new RegExp(pattern, "iu").exec(title);
  if (!found) return null;

  return [title.slice(0, found.index), found[0], title.slice(found.index + found[0].length)];
}
