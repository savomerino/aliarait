import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  async rewrites() {
    return [
      { source: "/index.html", destination: "/" },
      { source: "/404.html", destination: "/404" },
      { source: "/rrss/index.html", destination: "/rrss" },
      { source: "/rrss/index_2025_OCTUBRE.html", destination: "/rrss/2025/octubre" },
      { source: "/rrss/AliaraIT_2025_SEPT.html", destination: "/rrss/2025/septiembre" },
      { source: "/rrss/AliaraIT_2025_AGO.html", destination: "/rrss/2025/agosto" },
      { source: "/Mailing/MailingClaro_.html", destination: "/api/mailing/claro" },
      { source: "/Mailing/MailingOscuro_.html", destination: "/api/mailing/oscuro" },
      { source: "/forms/contact.php", destination: "/api/contact" },
      { source: "/forms/newsletter.php", destination: "/api/newsletter" },
    ];
  },
};

export default nextConfig;
