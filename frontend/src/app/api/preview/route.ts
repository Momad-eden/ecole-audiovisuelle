import { redirect } from "next/navigation";
import type { NextRequest } from "next/server";

export function GET(request: NextRequest) {
  const token = request.nextUrl.searchParams.get("token") ?? "";
  redirect(`/apercu?token=${encodeURIComponent(token)}`);
}
