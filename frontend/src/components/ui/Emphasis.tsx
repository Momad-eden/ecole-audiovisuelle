import { titleParts } from "@/lib/emphasis";
import { frenchSpacing } from "@/lib/utils";

/** Titre avec ses mots mis en valeur (*mot*) en italique élégant, couleur de la rubrique. */
export function Emphasis({ text }: { text: string | null | undefined }) {
  return (
    <>
      {titleParts(frenchSpacing(text)).map((part, index) =>
        part.accent ? <em key={index} className="title-accent">{part.text}</em> : <span key={index}>{part.text}</span>,
      )}
    </>
  );
}
