import { timingSafeEqual } from "node:crypto";
import { revalidateTag } from "next/cache";
import { NextResponse, type NextRequest } from "next/server";
import { CONTENT_TAG } from "@/lib/api";

/** Appelée par Laravel après chaque publication : les pages se régénèrent à la visite suivante. */
export async function POST(request: NextRequest) {
  const body = (await request.json().catch(() => null)) as { secret?: string; tags?: string[] } | null;
  const secret = process.env.REVALIDATE_SECRET;

  const provided = Buffer.from(body?.secret ?? "");
  const expected = Buffer.from(secret ?? "");
  if (!secret || provided.length !== expected.length || !timingSafeEqual(provided, expected)) {
    return NextResponse.json({ revalidated: false }, { status: 401 });
  }

  const tags = body?.tags && body.tags.length > 0 ? body.tags : [CONTENT_TAG];
  // expire: 0 : la personne qui vient de publier voit immédiatement sa modification.
  tags.forEach((tag) => revalidateTag(tag, { expire: 0 }));

  return NextResponse.json({ revalidated: true, tags });
}
