/**
 * Mots mis en valeur dans un titre : l'équipe les entoure d'astérisques dans l'admin
 * (« l'art comme *métier* »). Ils s'affichent en italique élégant, dans la couleur de la rubrique.
 */
export type TitlePart = { text: string; accent: boolean };

/** Découpe un titre en morceaux normaux et mis en valeur ; un astérisque seul reste tel quel. */
export function titleParts(title: string | null | undefined): TitlePart[] {
  if (!title) return [];
  const parts: TitlePart[] = [];
  const pattern = /\*([^*\n]+)\*/g;
  let last = 0;
  for (const match of title.matchAll(pattern)) {
    if (match.index! > last) parts.push({ text: title.slice(last, match.index), accent: false });
    parts.push({ text: match[1], accent: true });
    last = match.index! + match[0].length;
  }
  if (last < title.length) parts.push({ text: title.slice(last), accent: false });
  return parts;
}

/** Le titre sans ses astérisques (étiquettes accessibles, métadonnées). */
export function plainTitle(title: string | null | undefined): string {
  return titleParts(title).map((part) => part.text).join("");
}

/**
 * Mots du titre (dévoilement mot à mot), séparés aux espaces ; un mot peut mêler une partie mise en valeur
 * et de la ponctuation collée (« *héritage*, » reste un seul mot, sans espace avant la virgule).
 */
export function titleWords(title: string | null | undefined): TitlePart[][] {
  const words: TitlePart[][] = [[]];
  for (const part of titleParts(title)) {
    part.text.split(/(\s+)/).forEach((piece) => {
      if (piece === "") return;
      if (/^\s+$/.test(piece)) {
        if (words.at(-1)!.length > 0) words.push([]);
        return;
      }
      words.at(-1)!.push({ text: piece, accent: part.accent });
    });
  }
  return words.filter((word) => word.length > 0);
}
