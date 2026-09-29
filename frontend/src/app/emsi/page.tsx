import { CmsPageContent, cmsMetadata } from "@/lib/cms-page";

export const generateMetadata = () => cmsMetadata("emsi");

export default function EmsiPage() {
  return <CmsPageContent slug="emsi" />;
}
