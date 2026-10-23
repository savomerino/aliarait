import type { Metadata } from "next";
import LegacyHtmlApp from "@/components/legacy/LegacyHtmlApp";

export const metadata: Metadata = { title: "Plan de Contenido Aliara IT - Septiembre 2025" };

export default function Page() {
  return <LegacyHtmlApp src="/legacy/rrss/AliaraIT_2025_SEPT.html" />;
}
