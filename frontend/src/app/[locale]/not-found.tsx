import { NotFoundContent } from "@/components/NotFoundContent";

/**
 * Page 404 : rendue dans le layout [locale] sans paramètres de route ; le contenu (client) lit la langue
 * dans le fournisseur du layout et donne lui-même son titre de document.
 */
export default function NotFound() {
  return <NotFoundContent />;
}
