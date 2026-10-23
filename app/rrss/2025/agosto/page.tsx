import type { Metadata } from "next";
import LegacyHtmlApp from "@/components/legacy/LegacyHtmlApp";

export const metadata: Metadata = { title: "Plan de Contenido Aliara IT - Agosto 2025" };

export default function Page() {
  return <LegacyHtmlApp src="/legacy/rrss/AliaraIT_2025_AGO.html" />;
}
