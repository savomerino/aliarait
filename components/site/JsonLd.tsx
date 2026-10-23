import { site } from "@/lib/site";

export default function JsonLd() {
  const data = {
    "@context": "https://schema.org",
    "@type": "ProfessionalService",
    name: site.name,
    url: site.url,
    image: `${site.url}/assets/img/hero-img.png`,
    logo: `${site.url}/assets/img/web_logo.webp`,
    description: site.description,
    email: site.email,
    telephone: site.phoneE164,
    address: {
      "@type": "PostalAddress",
      addressLocality: "Resistencia",
      addressRegion: "Chaco",
      addressCountry: "AR",
    },
    areaServed: {
      "@type": "Country",
      name: "Argentina",
    },
    knowsAbout: site.services.map((service) => service.name),
    hasOfferCatalog: {
      "@type": "OfferCatalog",
      name: "Servicios de infraestructura IT",
      itemListElement: site.services.map((service) => ({
        "@type": "Offer",
        itemOffered: {
          "@type": "Service",
          name: service.name,
          description: service.description,
        },
      })),
    },
  };

  return (
    <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(data) }} />
  );
}
