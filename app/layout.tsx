import type { Metadata } from "next";
import { site } from "@/lib/site";
import "../public/assets/vendor/bootstrap/css/bootstrap.min.css";
import "../public/assets/vendor/bootstrap-icons/bootstrap-icons.css";
import "../public/assets/vendor/aos/aos.css";
import "../public/assets/vendor/glightbox/css/glightbox.min.css";
import "../public/assets/vendor/swiper/swiper-bundle.min.css";
import "../public/assets/css/main.css";
import "./globals.css";

export const metadata: Metadata = {
  metadataBase: new URL(site.url),
  title: {
    default: "Aliara IT - Soluciones en Redes, Servidores y Soporte",
    template: "%s | Aliara IT",
  },
  description: site.description,
  applicationName: site.name,
  authors: [{ name: site.name, url: site.url }],
  creator: site.name,
  keywords: [
    "Aliara IT",
    "infraestructura IT",
    "redes Ubiquiti",
    "cableado estructurado",
    "racks",
    "servidores",
    "seguridad informática",
    "soporte técnico",
    "hardware",
    "software",
    "Resistencia",
    "Chaco",
  ],
  alternates: {
    canonical: "/",
  },
  robots: {
    index: true,
    follow: true,
  },
  icons: {
    icon: "/assets/img/favicon.png",
    apple: "/assets/img/apple-touch-icon.png",
  },
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="es">
      <head>
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossOrigin="" />
        <link
          href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Jost:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
          rel="stylesheet"
        />
        <meta name="theme-color" content="#1a1a1a" />
      </head>
      <body className="index-page">{children}</body>
    </html>
  );
}
