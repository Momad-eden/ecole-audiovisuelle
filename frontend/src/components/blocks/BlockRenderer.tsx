import type { Block } from "@/lib/types";
import { AudioBlock } from "./AudioBlock";
import { ArtworksBlock, NewsBlock, PartnersBlock, ProfessionalSpaceBlock, ProgramsBlock, RoomsBlock } from "./CollectionBlocks";
import { ContactBlock } from "./ContactBlock";
import { CardsBlock, CtaBlock, FaqBlock, GalleryBlock, QuoteBlock, StatsBlock, TextBlock, TextImageBlock, TimelineBlock, VideoBlock } from "./ContentBlocks";
import { HeroBlock } from "./HeroBlock";
import { CampusesBlock } from "./CampusesBlock";
import { CampusProgramsBlock } from "./CampusProgramsBlock";
import { DomainsBlock } from "./DomainsBlock";
import { DownloadsBlock } from "./DownloadsBlock";
import { SupportFormBlock } from "./SupportFormBlock";
import { AgendaBlock, BookingFormBlock, EcosystemBlock, EquipmentListBlock, PacksBlock, PlacesBlock, ProductionsBlock, ServicesBlock } from "./ImpactBlocks";
import { EquipmentBlock, MarqueeBlock, VenueBlock } from "./ShowcaseBlocks";
import type * as T from "./types";
import { leadsSomewhere, pageAnchors } from "./anchors";

const withReachableButtons = (data: T.HeroData, path: string, anchors: Set<string>): T.HeroData =>
  data.buttons ? { ...data, buttons: data.buttons.filter((button) => leadsSomewhere(button.url, path, anchors)) } : data;

/** Blocs d'ouverture plein écran, posés sous l'en-tête transparent. */
const FULL_BLEED = new Set<string | undefined>(["hero", "domains"]);

/**
 * Un composant par type de bloc de l'administration (même nom). Un type inconnu est ignoré.
 * Une page qui ne s'ouvre pas sur un héros (ou le triptyque, plein écran) laisse la place de l'en-tête fixe.
 * `title` (titre de la page) sert de h1 invisible quand le bloc d'ouverture n'en fournit pas.
 */
export function BlockRenderer({ blocks, path = "", title }: { blocks: Block[]; path?: string; title?: string }) {
  const anchors = pageAnchors(blocks);
  return (
    <div className={FULL_BLEED.has(blocks[0]?.type) ? undefined : "pt-20"}>
      {blocks.map((block, index) => {
        const d = block.data as never;
        switch (block.type) {
          case "hero": return <HeroBlock key={block.id} data={withReachableButtons(d as T.HeroData, path, anchors)} first={index === 0} />;
          case "ecosystem": return <EcosystemBlock key={block.id} data={d as never} />;
          case "services": return <ServicesBlock key={block.id} data={d as never} />;
          case "equipment_list": return <EquipmentListBlock key={block.id} data={d as never} />;
          case "packs": return <PacksBlock key={block.id} data={d as never} />;
          case "productions": return <ProductionsBlock key={block.id} data={d as never} />;
          case "agenda": return <AgendaBlock key={block.id} data={d as never} />;
          case "booking_form": return <BookingFormBlock key={block.id} data={d as never} />;
          case "campuses": return <CampusesBlock key={block.id} data={d as never} />;
          case "domains": return <DomainsBlock key={block.id} data={d as T.DomainsData} first={index === 0} pageTitle={title} />;
          case "campus_programs": return <CampusProgramsBlock key={block.id} id={block.id} data={d as T.CampusProgramsData} />;
          case "downloads": return <DownloadsBlock key={block.id} data={d as T.DownloadsData} />;
          case "support_form": return <SupportFormBlock key={block.id} data={d as T.SupportFormData} first={index === 0} />;
          case "places": return <PlacesBlock key={block.id} data={d as never} />;
          case "marquee": return <MarqueeBlock key={block.id} data={d as T.MarqueeData} />;
          case "venue": return <VenueBlock key={block.id} data={d as T.VenueData} />;
          case "equipment": return <EquipmentBlock key={block.id} data={d as T.EquipmentData} />;
          case "text": return <TextBlock key={block.id} data={d as T.TextData} />;
          case "text_image": return <TextImageBlock key={block.id} data={d as T.TextImageData} />;
          case "gallery": return <GalleryBlock key={block.id} data={d as T.GalleryData} />;
          case "video": return <VideoBlock key={block.id} data={d as T.VideoData} />;
          case "audio": return <AudioBlock key={block.id} data={d as T.AudioData} />;
          case "stats": return <StatsBlock key={block.id} data={d as T.StatsData} />;
          case "quote": return <QuoteBlock key={block.id} data={d as T.QuoteData} />;
          case "cta": return <CtaBlock key={block.id} data={d as T.CtaData} />;
          case "cards": return <CardsBlock key={block.id} data={d as T.CardsData} />;
          case "timeline": return <TimelineBlock key={block.id} data={d as T.TimelineData} />;
          case "faq": return <FaqBlock key={block.id} data={d as T.FaqData} />;
          case "programs": return <ProgramsBlock key={block.id} data={d as T.ProgramsData} />;
          case "artworks": return <ArtworksBlock key={block.id} data={d as T.ArtworksData} />;
          case "rooms": return <RoomsBlock key={block.id} data={d as T.RoomsData} />;
          case "news": return <NewsBlock key={block.id} data={d as T.NewsData} />;
          case "partners": return <PartnersBlock key={block.id} data={d as T.PartnersData} />;
          case "professional_space": return <ProfessionalSpaceBlock key={block.id} data={d as T.ProfessionalSpaceData} />;
          case "contact": return <ContactBlock key={block.id} data={d as T.ContactData} />;
          default: return null;
        }
      })}
    </div>
  );
}
