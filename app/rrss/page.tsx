import type { Metadata } from "next";
import LegacyHtmlApp from "@/components/legacy/LegacyHtmlApp";

export const metadata: Metadata = { title: "Plan de Contenido Interactivo - Aliara IT 2026" };

export default function Page() {
  return <LegacyHtmlApp src="/legacy/rrss/index.html" />;
}
