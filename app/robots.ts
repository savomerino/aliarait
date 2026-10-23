import type { MetadataRoute } from "next";
import { site } from "@/lib/site";

const aiAgents = [
  "GPTBot",
  "ChatGPT-User",
  "OAI-SearchBot",
  "ClaudeBot",
  "anthropic-ai",
  "PerplexityBot",
  "Google-Extended",
  "Applebot-Extended",
  "Bytespider",
  "cohere-ai",
];

export default function robots(): MetadataRoute.Robots {
  return {
    rules: [
      {
        userAgent: "*",
        allow: "/",
        disallow: ["/api/", "/rrss", "/mailing", "/legacy"],
      },
      ...aiAgents.map((userAgent) => ({
        userAgent,
        allow: ["/", "/llms.txt", "/llms-full.txt"],
        disallow: ["/api/", "/rrss", "/mailing", "/legacy"],
      })),
    ],
    sitemap: `${site.url}/sitemap.xml`,
    host: site.url,
  };
}
