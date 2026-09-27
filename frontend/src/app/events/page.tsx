import { CmsPageContent, cmsMetadata } from "@/lib/cms-page";

export const generateMetadata = () => cmsMetadata("events");

export default function EventsPage() {
  return <CmsPageContent slug="events" />;
}
