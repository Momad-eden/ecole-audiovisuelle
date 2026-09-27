"use client";

import dynamic from "next/dynamic";

/** Formulaire de demande chargé à la demande (validation zod) et rendu côté navigateur uniquement. */
export const LazyBookingForm = dynamic(() => import("./BookingForm").then((m) => m.BookingForm), {
  ssr: false,
  loading: () => <div className="min-h-[36rem] rounded-3xl border border-line bg-night-2" aria-busy="true" />,
});
