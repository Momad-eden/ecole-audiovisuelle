import type { Block } from "@/lib/types";
import { AudioBlock } from "./AudioBlock";
import { ArtworksBlock, NewsBlock, PartnersBlock, ProfessionalSpaceBlock, ProgramsBlock, RoomsBlock } from "./CollectionBlocks";
import { ContactBlock } from "./ContactBlock";
import { CardsBlock, CtaBlock, FaqBlock, GalleryBlock, QuoteBlock, StatsBlock, TextBlock, TextImageBlock, TimelineBlock, VideoBlock } from "./ContentBlocks";
import { HeroBlock } from "./HeroBlock";
import { AgendaBlock, BookingFormBlock, EcosystemBlock, EquipmentListBlock, PacksBlock, PlacesBlock, ProductionsBlock, ServicesBlock } from "./ImpactBlocks";
import { EquipmentBlock, MarqueeBlock, VenueBlock } from "./ShowcaseBlocks";
import type * as T from "./types";

/**
 * Un composant par type de bloc de l'administration (même nom). Un type inconnu est ignoré.
 * Une page qui ne s'ouvre pas sur un héros laisse la place de l'en-tête fixe.
 */
export function BlockRenderer({ blocks }: { blocks: Block[] }) {
  return (
    <div className={blocks[0]?.type === "hero" ? undefined : "pt-20"}>
      {blocks.map((block, index) => {
        const d = block.data as never;
        switch (block.type) {
          case "hero": return <HeroBlock key={block.id} data={d as T.HeroData} first={index === 0} />;
          case "ecosystem": return <EcosystemBlock key={block.id} data={d as never} />;
          case "services": return <ServicesBlock key={block.id} data={d as never} />;
          case "equipment_list": return <EquipmentListBlock key={block.id} data={d as never} />;
          case "packs": return <PacksBlock key={block.id} data={d as never} />;
          case "productions": return <ProductionsBlock key={block.id} data={d as never} />;
          case "agenda": return <AgendaBlock key={block.id} data={d as never} />;
          case "booking_form": return <BookingFormBlock key={block.id} data={d as never} />;
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
