import { MessageCircle } from "lucide-react";

/** Bouton WhatsApp flottant : au Sénégal, c'est le premier réflexe pour poser une question. */
export function WhatsAppButton({ number }: { number: string | null }) {
  const digits = number?.replace(/[^0-9]/g, "");
  if (!digits) return null;

  return (
    <a
      href={`https://wa.me/${digits}?text=${encodeURIComponent("Bonjour, j'ai une question : ")}`}
      target="_blank"
      rel="noopener noreferrer"
      className="floating-action fixed bottom-6 right-4 z-40 grid size-14 place-items-center rounded-full bg-[#25d366] text-[#07070a] shadow-[0_10px_30px_-8px_rgb(37_211_102/0.7)] transition hover:scale-105 sm:right-6"
      aria-label="Nous écrire sur WhatsApp (nouvel onglet)"
    >
      <MessageCircle className="size-7" aria-hidden />
    </a>
  );
}
