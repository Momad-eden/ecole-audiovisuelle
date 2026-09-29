import { Download, FileText } from "lucide-react";
import { Section, SectionTitle } from "@/components/ui/Section";
import { fileLabel } from "@/lib/files";
import { frenchSpacing } from "@/lib/utils";
import type { DownloadsData } from "./types";

/** Documents à télécharger (PDF) : titre, description, type et poids ; ouverts dans un nouvel onglet. */
export function DownloadsBlock({ data }: { data: DownloadsData }) {
  const files = data.files ?? [];
  if (files.length === 0) return null;

  return (
    <Section>
      <SectionTitle title={data.title} />
      <ul className="max-w-4xl divide-y divide-line overflow-hidden rounded-[2rem] border border-line bg-night-2">
        {files.map((file) => {
          const label = fileLabel(file.extension, file.size);
          return (
            <li key={file.url}>
              <a
                href={file.url}
                target="_blank"
                rel="noopener"
                aria-label={`${file.title} (${label}, nouvel onglet)`}
                className="group flex items-center gap-5 p-6 transition-colors hover:bg-night-3 focus-visible:bg-night-3 sm:p-7"
              >
                <FileText className="size-8 shrink-0 text-[var(--accent-ink)]" aria-hidden />
                <span className="min-w-0 flex-1">
                  <span className="display block text-xl leading-tight">{frenchSpacing(file.title)}</span>
                  {file.description && <span className="mt-1 block text-ink-muted">{frenchSpacing(file.description)}</span>}
                  <span className="cartel mt-2 block">{label}</span>
                </span>
                <Download className="size-5 shrink-0 text-ink-muted transition-colors group-hover:text-[var(--accent-ink)]" aria-hidden />
              </a>
            </li>
          );
        })}
      </ul>
    </Section>
  );
}
