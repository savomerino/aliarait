import type { Metadata } from "next";
import SiteFrame from "@/components/site/SiteFrame";

export const metadata: Metadata = {
  title: "Aliara IT - Soluciones en Redes, Servidores y Soporte",
  description:
    "Especialistas en Gestión de Redes (Ubiquiti, Cableado, Racks), Servidores, Seguridad Informática y Soporte Técnico de Hardware y Software.",
};

export default function NotFound() {
  return (
    <SiteFrame mode="404">
      <main className="main">
        <section id="error-404" className="error-404 section">
          <div className="container" data-aos="fade-up" data-aos-delay="100">
            <div className="text-center">
              <div className="error-icon mb-4" data-aos="zoom-in" data-aos-delay="200">
                <i className="bi bi-exclamation-circle"></i>
              </div>
              <h1 className="error-code mb-4" data-aos="fade-up" data-aos-delay="300">404</h1>
              <h2 className="error-title mb-3" data-aos="fade-up" data-aos-delay="400">¡Ups! Página no encontrada</h2>
              <p className="error-text mb-4" data-aos="fade-up" data-aos-delay="500">
                La página que estás buscando podría haber sido eliminada, haber cambiado de nombre o no estar disponible temporalmente.
              </p>
              <div className="error-action" data-aos="fade-up" data-aos-delay="700">
                <a href="/" className="btn btn-primary">Volver al Inicio</a>
              </div>
            </div>
          </div>
        </section>
      </main>
    </SiteFrame>
  );
}
