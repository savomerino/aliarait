"use client";

import { useEffect, useState, type ReactNode } from "react";

function loadScript(src: string) {
  return new Promise<void>((resolve, reject) => {
    if (document.querySelector(`script[src="${src}"]`)) {
      resolve();
      return;
    }
    const script = document.createElement("script");
    script.src = src;
    script.async = false;
    script.onload = () => resolve();
    script.onerror = () => reject(new Error(src));
    document.body.appendChild(script);
  });
}

export default function SiteFrame({
  mode,
  children,
}: {
  mode: "home" | "404";
  children: ReactNode;
}) {
  const [open, setOpen] = useState(false);
  const [scrolled, setScrolled] = useState(false);
  const [showTop, setShowTop] = useState(false);
  const [active, setActive] = useState("#hero");
  const home = mode === "home";
  const href = (hash: string) => (home ? hash : `/${hash}`);

  useEffect(() => {
    const onScroll = () => {
      setScrolled(window.scrollY > 100);
      setShowTop(window.scrollY > 100);
      const links = ["#hero", "#about", "#services", "#contact"];
      const position = window.scrollY + 200;
      let current = "#hero";
      for (const hash of links) {
        const section = document.querySelector(hash);
        if (!section) continue;
        const top = (section as HTMLElement).offsetTop;
        const height = (section as HTMLElement).offsetHeight;
        if (position >= top && position <= top + height) current = hash;
      }
      setActive(current);
    };
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });

    const preloader = document.getElementById("preloader");
    const removePreloader = () => preloader?.remove();
    if (document.readyState === "complete") removePreloader();
    else window.addEventListener("load", removePreloader);

    document.querySelectorAll(".faq-item h3, .faq-item .faq-toggle").forEach((node) => {
      node.addEventListener("click", () => {
        node.parentElement?.classList.toggle("faq-active");
      });
    });

    const skills = document.querySelectorAll(".skills-animation");
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;
          entry.target.querySelectorAll<HTMLElement>(".progress .progress-bar").forEach((bar) => {
            bar.style.width = `${bar.getAttribute("aria-valuenow") || 0}%`;
          });
          observer.unobserve(entry.target);
        });
      },
      { rootMargin: "0px 0px -20% 0px", threshold: 0 }
    );
    skills.forEach((item) => observer.observe(item));

    if (window.location.hash && document.querySelector(window.location.hash)) {
      const section = document.querySelector(window.location.hash) as HTMLElement;
      const margin = parseInt(getComputedStyle(section).scrollMarginTop || "0", 10);
      window.setTimeout(() => {
        window.scrollTo({ top: section.offsetTop - margin, behavior: "smooth" });
      }, 100);
    }

    loadScript("/assets/vendor/aos/aos.js")
      .then(() => {
        window.AOS?.init({
          duration: 600,
          easing: "ease-in-out",
          once: true,
          mirror: false,
        });
        document.documentElement.classList.add("aos-ready");
      })
      .catch(() => undefined);

    return () => {
      window.removeEventListener("scroll", onScroll);
      window.removeEventListener("load", removePreloader);
      observer.disconnect();
      document.documentElement.classList.remove("aos-ready");
      document.body.classList.remove("mobile-nav-active", "scrolled");
    };
  }, []);

  useEffect(() => {
    document.body.classList.toggle("scrolled", scrolled);
  }, [scrolled]);

  useEffect(() => {
    document.body.classList.toggle("mobile-nav-active", open);
  }, [open]);

  const closeMenu = () => setOpen(false);

  return (
    <>
      <header id="header" className="header d-flex align-items-center fixed-top">
        <div className="container-fluid container-xl position-relative d-flex align-items-center">
          <a href={home ? "#hero" : "/"} className="logo d-flex align-items-center me-auto">
            <img src="/assets/img/web_logo.webp" alt="Aliara IT Logo" style={{ height: 40 }} />
          </a>
          <nav id="navmenu" className="navmenu">
            <ul>
              <li>
                <a href={href("#hero")} className={!home || active === "#hero" ? "active" : undefined} onClick={closeMenu}>
                  Inicio
                </a>
              </li>
              <li>
                <a href={href("#about")} className={home && active === "#about" ? "active" : undefined} onClick={closeMenu}>
                  Nosotros
                </a>
              </li>
              <li>
                <a href={href("#services")} className={home && active === "#services" ? "active" : undefined} onClick={closeMenu}>
                  Servicios
                </a>
              </li>
            </ul>
            <i
              className={`mobile-nav-toggle d-xl-none bi ${open ? "bi-x" : "bi-list"}`}
              onClick={() => setOpen((value) => !value)}
            />
          </nav>
          <a className="btn-getstarted" href={href("#contact")} onClick={closeMenu}>
            Contáctanos
          </a>
        </div>
      </header>

      {children}

      <footer id="footer" className="footer">
        <div className="container footer-top">
          <div className="row gy-4">
            <div className="col-lg-4 col-md-6 footer-about">
              <a href={home ? "#hero" : "/"} className="d-flex align-items-center">
                <img src="/assets/img/web_logo.webp" alt="Aliara IT Logo" style={{ height: 40 }} />
              </a>
              <div className="footer-contact pt-3">
                <h3>Ubicación</h3>
                <p>Resistencia, Chaco, Argentina</p>
                <h3>Teléfono</h3>
                <p>{home ? "+54 9 362 421-5757" : "+54 362 4122191"}</p>
                <p>
                  <strong>Email:</strong> <span>info@aliarait.com</span>
                </p>
              </div>
            </div>
            <div className="col-lg-2 col-md-3 footer-links">
              <h4>Enlaces Rápidos</h4>
              <ul>
                <li><a href={href("#hero")}>Inicio</a></li>
                <li><a href={href("#about")}>Nosotros</a></li>
                <li><a href={href("#services")}>Servicios</a></li>
                <li><a href="#">Términos de servicio</a></li>
                <li><a href="#">Política de privacidad</a></li>
              </ul>
            </div>
            <div className="col-lg-2 col-md-3 footer-links">
              <h4>Nuestros Servicios</h4>
              <ul>
                <li><a href={href("#services")}>Gestión de Redes</a></li>
                <li><a href={href("#services")}>Servidores</a></li>
                <li><a href={href("#services")}>Seguridad Informática</a></li>
                <li><a href={href("#services")}>Soporte Técnico</a></li>
              </ul>
            </div>
            <div className="col-lg-4 col-md-12">
              <h4>Seguinos</h4>
              <p>Seguinos en nuestras redes sociales para estar al día con las novedades y consejos de tecnología.</p>
              <div className="social-links d-flex">
                <a href=""><i className="bi bi-twitter-x"></i></a>
                <a href=""><i className="bi bi-facebook"></i></a>
                <a href=""><i className="bi bi-instagram"></i></a>
                <a href=""><i className="bi bi-linkedin"></i></a>
              </div>
            </div>
          </div>
        </div>
        <div className="container copyright text-center mt-4">
          <p>
            © <span>Copyright</span> <strong className="px-1 sitename">Aliara IT</strong>{" "}
            <span>Todos los Derechos Reservados</span>
          </p>
          <div className="credits">
            Diseñado por <a href="https://bulgaria.ar/">Bulgaria</a>
          </div>
        </div>
      </footer>

      <a
        href="#"
        id="scroll-top"
        className={`scroll-top d-flex align-items-center justify-content-center${showTop ? " active" : ""}`}
        onClick={(event) => {
          event.preventDefault();
          window.scrollTo({ top: 0, behavior: "smooth" });
        }}
      >
        <i className="bi bi-arrow-up-short"></i>
      </a>
      <div id="preloader"></div>
    </>
  );
}
