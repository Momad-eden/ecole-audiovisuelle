"use client";

import Link from "next/link";
import type { ComponentProps } from "react";
import { localizedPath } from "@/lib/i18n/locales";
import { useLocale } from "./LocaleProvider";

type Props = Omit<ComponentProps<typeof Link>, "href"> & { href: string };

/**
 * Lien interne dans la langue de la page : à utiliser à la place de next/link partout où l'adresse
 * est construite ou vient de l'API (toujours française, ex. /emsi/dakar → /en/emsi/dakar en anglais).
 */
export function LocaleLink({ href, ...props }: Props) {
  return <Link href={localizedPath(href, useLocale())} {...props} />;
}
