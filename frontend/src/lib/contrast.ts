/**
 * Couleur de texte lisible sur un aplat de couleur saisi dans l'administration :
 * presque noir sur une couleur claire ou vive, blanc sur une couleur sombre (WCAG).
 */

const DARK_INK = "#07070a";
const LIGHT_INK = "#ffffff";

function parse(color: string): [number, number, number] | null {
  const value = color.trim().toLowerCase();
  const hex = value.match(/^#([0-9a-f]{3}|[0-9a-f]{6})$/);
  if (hex) {
    const digits = hex[1].length === 3 ? [...hex[1]].map((d) => d + d) : hex[1].match(/../g)!;
    return digits.map((d) => parseInt(d, 16)) as [number, number, number];
  }
  const rgb = value.match(/^rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/);
  return rgb ? [Number(rgb[1]), Number(rgb[2]), Number(rgb[3])] : null;
}

function luminance([r, g, b]: [number, number, number]): number {
  const linear = (c: number) => (c / 255 <= 0.03928 ? c / 255 / 12.92 : ((c / 255 + 0.055) / 1.055) ** 2.4);
  return 0.2126 * linear(r) + 0.7152 * linear(g) + 0.0722 * linear(b);
}

const contrast = (a: number, b: number) => (Math.max(a, b) + 0.05) / (Math.min(a, b) + 0.05);

/**
 * `darken` : en thème clair, l'aplat est la couleur mélangée à 45 % de noir
 * (`color-mix(in oklab, couleur 55%, black)`), soit une luminance multipliée par 0,55³.
 * Renvoie null si la couleur n'est pas lisible (la couleur du thème s'applique alors).
 */
export function readableInk(color: string, { darken = false } = {}): string | null {
  const rgb = parse(color);
  if (!rgb) return null;
  const background = luminance(rgb) * (darken ? 0.55 ** 3 : 1);
  return contrast(background, luminance([7, 7, 10])) >= contrast(background, 1) ? DARK_INK : LIGHT_INK;
}

/**
 * Style d'un élément coloré par une couleur saisie dans l'admin : la couleur et, quand elle est lisible,
 * la couleur du texte posé dessus dans chaque thème (reprise par `text-on-accent`, voir globals.css).
 */
export function accentVars(accent?: string | null): Record<string, string> | undefined {
  if (!accent) return undefined;
  const on = readableInk(accent);
  const onLight = readableInk(accent, { darken: true });
  return on && onLight ? { "--accent": accent, "--accent-on": on, "--accent-on-light": onLight } : { "--accent": accent };
}
