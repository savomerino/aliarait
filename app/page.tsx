import type { Metadata } from "next";
import ContactForm from "@/components/site/ContactForm";
import JsonLd from "@/components/site/JsonLd";
import SiteFrame from "@/components/site/SiteFrame";
import { site } from "@/lib/site";

export const metadata: Metadata = {
  title: "Aliara IT - Soluciones en Redes, Servidores y Soporte",
  description: site.description,
  openGraph: {
    type: "website",
    locale: "es_AR",
    url: site.url,
    siteName: site.name,
    title: "Aliara IT - Soluciones en Redes, Servidores y Soporte",
    description: site.description,
    images: [
      {
        url: "/assets/img/hero-img.png",
        alt: "Aliara IT, infraestructura de redes, servidores y soporte",
      },
    ],
  },
  twitter: {
    card: "summary_large_image",
    title: "Aliara IT - Soluciones en Redes, Servidores y Soporte",
    description: site.description,
    images: ["/assets/img/hero-img.png"],
  },
};

export default function HomePage() {
  return (
    <SiteFrame mode="home">
      <JsonLd />
      <main className="main">
        <section id="hero" className="hero section dark-background">
          <div className="container">
            <div className="row gy-4">
              <div className="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center" data-aos="zoom-out">
                <h1>Somos tu Aliado Tecnológico de Confianza</h1>
                <p>Soluciones expertas en Redes, Servidores, Seguridad y Soporte Técnico para garantizar la continuidad de tu negocio.</p>
                <div className="d-flex">
                  <a href="#services" className="btn-get-started">Nuestros Servicios</a>
                </div>
              </div>
              <div className="col-lg-6 order-1 order-lg-2 hero-img" data-aos="zoom-out" data-aos-delay="200">
                <img src="/assets/img/hero-img.png" className="img-fluid animated" alt="Imagen Hero de Aliara IT" />
              </div>
            </div>
          </div>
        </section>

        <section id="about" className="about section">
          <div className="container section-title" data-aos="fade-up">
            <h2>Nosotros</h2>
          </div>
          <div className="container">
            <div className="row gy-4">
              <div className="col-lg-6 content" data-aos="fade-up" data-aos-delay="100">
                <h3>Nacimos de la idea de unir, vincular y potenciar</h3>
                <p>
                  En Aliara IT, nuestro nombre define nuestra filosofía. Deriva del latín &quot;alligare&quot; (unir, vincular), y expresa nuestro compromiso de ser el socio estratégico que tu empresa necesita para crecer.
                </p>
                <ul>
                  <li><i className="bi bi-check-circle-fill"></i><span><strong>Misión:</strong> Proveer soluciones expertas en redes, servidores, seguridad y soporte, asegurando la máxima eficiencia y continuidad operativa.</span></li>
                  <li><i className="bi bi-check-circle-fill"></i><span><strong>Visión:</strong> Ser los referentes en infraestructura IT, reconocidos por nuestra confiabilidad, conocimiento técnico y compromiso con el éxito de nuestros clientes.</span></li>
                  <li><i className="bi bi-check-circle-fill"></i><span><strong>Valores:</strong> Compromiso, Confianza, Crecimiento Colaborativo y Cercanía.</span></li>
                </ul>
              </div>
              <div className="col-lg-6" data-aos="fade-up" data-aos-delay="200">
                <p>No solo proveemos tecnología; forjamos alianzas para potenciar tu infraestructura, prevenir problemas y mejorar la eficiencia operativa. Más que proveedores, somos tu equipo extendido de expertos en IT, dedicados a entender tus desafíos y convertirlos en oportunidades.</p>
                <a href="#contact" className="read-more"><span>Hablemos de tu Proyecto</span><i className="bi bi-arrow-right"></i></a>
              </div>
            </div>
          </div>
        </section>

        <section id="why-us" className="section why-us light-background">
          <div className="container-fluid">
            <div className="row gy-4">
              <div className="col-lg-7 d-flex flex-column justify-content-center order-2 order-lg-1">
                <div className="content px-xl-5" data-aos="fade-up" data-aos-delay="100">
                  <h3>¿Por Qué Elegir Aliara IT?</h3>
                  <p>
                    Porque no solo resolvemos problemas, los anticipamos. Nuestro enfoque proactivo y nuestro compromiso con la excelencia nos convierten en el socio tecnológico que tu empresa necesita para operar sin interrupciones y crecer de forma segura.
                  </p>
                </div>
                <div className="faq-container px-xl-5" data-aos="fade-up" data-aos-delay="200">
                  <div className="faq-item faq-active">
                    <h3><span>01</span> ¿Cómo gestionan las redes y qué tecnologías usan?</h3>
                    <div className="faq-content">
                      <p>Nos especializamos en el diseño e implementación de redes de alto rendimiento, utilizando principalmente tecnología Ubiquiti. Realizamos cableado estructurado profesional y organización de racks para garantizar una red ordenada, escalable y fácil de mantener.</p>
                    </div>
                    <i className="faq-toggle bi bi-chevron-right"></i>
                  </div>
                  <div className="faq-item">
                    <h3><span>02</span> ¿Qué tipo de soluciones de seguridad informática ofrecen?</h3>
                    <div className="faq-content">
                      <p>Implementamos una estrategia de seguridad integral que incluye firewalls de última generación, sistemas de antivirus centralizados, y la creación de políticas de seguridad robustas para proteger los datos y la infraestructura de tu empresa contra todo tipo de amenazas.</p>
                    </div>
                    <i className="faq-toggle bi bi-chevron-right"></i>
                  </div>
                  <div className="faq-item">
                    <h3><span>03</span> ¿Qué abarca el servicio de Soporte Técnico?</h3>
                    <div className="faq-content">
                      <p>Nuestro soporte técnico cubre todas las necesidades de tu empresa, tanto a nivel de hardware (reparación y mantenimiento de equipos) como de software (resolución de problemas de sistema operativo, aplicaciones, etc.). Brindamos asistencia remota y presencial para minimizar el tiempo de inactividad.</p>
                    </div>
                    <i className="faq-toggle bi bi-chevron-right"></i>
                  </div>
                </div>
              </div>
              <div className="col-lg-5 order-1 order-lg-2 why-us-img">
                <img src="/assets/img/why-us.png" className="img-fluid" alt="Por qué elegir Aliara IT" data-aos="zoom-in" data-aos-delay="100" />
              </div>
            </div>
          </div>
        </section>

        <section id="skills" className="skills section">
          <div className="container" data-aos="fade-up" data-aos-delay="100">
            <div className="row">
              <div className="col-lg-6 d-flex align-items-center">
                <img src="/assets/img/illustration/illustration-10.webp" className="img-fluid" alt="Habilidades de Aliara IT" />
              </div>
              <div className="col-lg-6 pt-4 pt-lg-0 content">
                <h3>Nuestras Capacidades a tu Servicio</h3>
                <p>
                  Contamos con un equipo de especialistas certificados y con amplia experiencia en las áreas críticas que tu empresa necesita para operar con éxito en el entorno digital actual.
                </p>
                <div className="skills-content skills-animation">
                  {[
                    ["Gestión de Redes (Ubiquiti, Cableado, Racks)", "100"],
                    ["Administración de Servidores", "100"],
                    ["Seguridad Informática", "100"],
                    ["Soporte Técnico (Hardware & Software)", "100"],
                  ].map(([label, value]) => (
                    <div className="progress" key={label}>
                      <span className="skill"><span>{label}</span> <i className="val">{value}%</i></span>
                      <div className="progress-bar-wrap">
                        <div className="progress-bar" role="progressbar" aria-valuenow={Number(value)} aria-valuemin={0} aria-valuemax={100}></div>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          </div>
        </section>

        <section id="services" className="services section light-background">
          <div className="container section-title" data-aos="fade-up">
            <h2>Servicios Especializados</h2>
            <p>Soluciones integrales para construir y mantener una infraestructura tecnológica sólida, segura y eficiente.</p>
          </div>
          <div className="container">
            <div className="row gy-4">
              {[
                ["100", "bi-diagram-3", "Gestión de Redes", "Diseño e implementación de redes robustas. Expertos en Ubiquiti, cableado estructurado y organización de racks."],
                ["200", "bi-server", "Servidores", "Instalación, configuración y mantenimiento de servidores físicos y virtuales para un rendimiento óptimo y seguro."],
                ["300", "bi-shield-lock", "Seguridad Informática", "Protegemos tus activos digitales con soluciones de firewall, antivirus y políticas de seguridad a medida."],
                ["400", "bi-tools", "Soporte Técnico", "Asistencia especializada en hardware y software para resolver cualquier incidencia y garantizar la continuidad operativa."],
              ].map(([delay, icon, title, text]) => (
                <div className="col-xl-3 col-md-6 d-flex" data-aos="fade-up" data-aos-delay={delay} key={title}>
                  <div className="service-item position-relative">
                    <div className="icon"><i className={`bi ${icon}`}></i></div>
                    <h4>{title}</h4>
                    <p>{text}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </section>

        <section id="call-to-action" className="call-to-action section dark-background">
          <img src="/assets/img/bg/bg-8.webp" alt="" />
          <div className="container">
            <div className="row" data-aos="zoom-in" data-aos-delay="100">
              <div className="col-xl-9 text-center text-xl-start">
                <h3>¿Listo para optimizar tu infraestructura IT?</h3>
                <p>Hablemos de tus desafíos y objetivos. Analizamos tus necesidades y te ofrecemos un plan de acción a medida, sin compromiso. Es hora de convertir tu infraestructura tecnológica en una ventaja competitiva.</p>
              </div>
              <div className="col-xl-3 cta-btn-container text-center">
                <a className="cta-btn align-middle" href="#contact">Solicitar una Consultoría</a>
              </div>
            </div>
          </div>
        </section>

        <section id="contact" className="contact section">
          <div className="container section-title" data-aos="fade-up">
            <h2>Contacto</h2>
            <p>¿Tenés alguna pregunta o necesitás una solución a medida? No dudes en contactarnos. Estamos aquí para ayudarte.</p>
          </div>
          <div className="container" data-aos="fade-up" data-aos-delay="100">
            <div className="row gy-4">
              <div className="col-lg-5">
                <div className="info-wrap">
                  <div className="info-item d-flex" data-aos="fade-up" data-aos-delay="200">
                    <i className="bi bi-geo-alt flex-shrink-0"></i>
                    <div>
                      <h3>Ubicación</h3>
                      <p>Resistencia, Chaco, Argentina</p>
                    </div>
                  </div>
                  <div className="info-item d-flex" data-aos="fade-up" data-aos-delay="300">
                    <i className="bi bi-telephone flex-shrink-0"></i>
                    <div>
                      <h3>Teléfono</h3>
                      <p>+54 9 362 421-5757</p>
                    </div>
                  </div>
                  <div className="info-item d-flex" data-aos="fade-up" data-aos-delay="400">
                    <i className="bi bi-envelope flex-shrink-0"></i>
                    <div>
                      <h3>Email</h3>
                      <p>info@aliarait.com</p>
                    </div>
                  </div>
                </div>
              </div>
              <div className="col-lg-7 d-flex align-items-center">
                <ContactForm />
              </div>
            </div>
          </div>
        </section>
      </main>
    </SiteFrame>
  );
}
