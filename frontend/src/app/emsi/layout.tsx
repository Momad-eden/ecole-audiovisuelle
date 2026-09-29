import { FixedDomainLayout } from "@/lib/domain-layout";

export default function Layout({ children }: { children: React.ReactNode }) {
  return <FixedDomainLayout domain="emsi">{children}</FixedDomainLayout>;
}
