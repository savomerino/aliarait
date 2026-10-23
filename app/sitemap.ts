import type { MetadataRoute } from "next";
import { site } from "@/lib/site";

export default function sitemap(): MetadataRoute.Sitemap {
  return [
    {
      url: site.url,
      lastModified: new Date("2026-10-21"),
      changeFrequency: "monthly",
      priority: 1,
    },
    {
      url: `${site.url}/llms.txt`,
      lastModified: new Date("2026-10-21"),
      changeFrequency: "monthly",
      priority: 0.4,
    },
  ];
}
