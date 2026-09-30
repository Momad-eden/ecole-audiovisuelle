import { CmsPageContent, cmsMetadata } from "@/lib/cms-page";

type Props = { params: Promise<{ slug: string[] }> };

/** Pages à blocs de l'école (/emsi/dakar, /emsi/saint-louis…), gérées dans l'administration. */
export async function generateMetadata({ params }: Props) {
  return cmsMetadata(`emsi/${(await params).slug.join("/")}`);
}

export default async function EmsiCmsPage({ params }: Props) {
  return <CmsPageContent slug={`emsi/${(await params).slug.join("/")}`} />;
}
