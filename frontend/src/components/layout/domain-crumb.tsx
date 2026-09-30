"use client";

import { createContext, useContext, useEffect, useState } from "react";

type CrumbContextValue = { title: string | null; setTitle: (title: string | null) => void };

const CrumbContext = createContext<CrumbContextValue>({ title: null, setTitle: () => {} });

export function CrumbProvider({ children }: { children: React.ReactNode }) {
  const [title, setTitle] = useState<string | null>(null);
  return <CrumbContext.Provider value={{ title, setTitle }}>{children}</CrumbContext.Provider>;
}

export const useCrumbTitle = () => useContext(CrumbContext).title;

/** Termine le fil d'Ariane du domaine par le titre de la page de détail (formation, réalisation, événement…). */
export function CurrentCrumb({ title }: { title: string }) {
  const { setTitle } = useContext(CrumbContext);
  useEffect(() => {
    setTitle(title);
    return () => setTitle(null);
  }, [title, setTitle]);
  return null;
}
