import { PlayButton } from "@/components/audio/PlayButton";
import { Section, SectionTitle } from "@/components/ui/Section";
import type { AudioData, BlockProps } from "./types";

export function AudioBlock({ data }: BlockProps<AudioData>) {
  const tracks = (data.tracks ?? []).filter((track) => track.url);
  if (tracks.length === 0) return null;
  return (
    <Section>
      <SectionTitle title={data.title} text={data.description} />
      <ol className="divide-y divide-line rounded-3xl border border-line bg-night-2">
        {tracks.map((track, index) => (
          <li key={track.url} className="flex items-center gap-5 p-5">
            <span className="cartel w-6">{String(index + 1).padStart(2, "0")}</span>
            <div className="min-w-0 flex-1">
              <p className="font-medium">{track.title}</p>
              {track.credits && <p className="text-sm text-ink-muted">{track.credits}</p>}
            </div>
            <PlayButton track={{ src: track.url!, title: track.title, subtitle: track.credits }} className="px-4 py-2 text-sm" />
          </li>
        ))}
      </ol>
    </Section>
  );
}
