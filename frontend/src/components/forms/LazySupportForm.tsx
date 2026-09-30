"use client";

import dynamic from "next/dynamic";

/** Formulaire « Nous soutenir » chargé à la demande, comme le formulaire de contact (voir LazyContactForm). */
export const LazySupportForm = dynamic(() => import("./SupportForm").then((m) => m.SupportForm), {
  ssr: false,
  loading: () => <div className="min-h-96 rounded-3xl border border-line bg-night-2" aria-busy="true" />,
});
