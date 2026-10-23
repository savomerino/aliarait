"use client";

import { useState } from "react";
import { site } from "@/lib/site";

export default function ContactForm() {
  const [validated, setValidated] = useState(false);
  const [sending, setSending] = useState(false);
  const [label, setLabel] = useState("Enviar Mensaje");

  async function onSubmit(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault();
    const form = event.currentTarget;
    setValidated(true);
    if (!form.checkValidity()) return;

    const name = (form.elements.namedItem("name") as HTMLInputElement).value.trim();
    const email = (form.elements.namedItem("email") as HTMLInputElement).value.trim();
    const message = (form.elements.namedItem("message") as HTMLTextAreaElement).value.trim();
    if (!name || !email || !message) {
      alert("Por favor, completa todos los campos antes de enviar el mensaje.");
      return;
    }

    setSending(true);
    setLabel("Enviando...");
    try {
      const response = await fetch("/api/contact", {
        method: "POST",
        headers: { "Content-Type": "application/json", Accept: "application/json" },
        body: JSON.stringify({ name, email, message }),
      });
      const data = await response.json().catch(() => ({}));
      if (response.ok) {
        form.reset();
        setValidated(false);
        alert("¡Mensaje enviado con éxito!");
      } else {
        alert(data.error || "Oops! Hubo un problema al enviar tu mensaje. Por favor, inténtalo de nuevo.");
      }
    } catch {
      alert("Oops! Hubo un problema de red al enviar tu mensaje. Por favor, revisa tu conexión e inténtalo de nuevo.");
    } finally {
      setSending(false);
      setLabel("Enviar Mensaje");
    }
  }

  function sendWhatsapp() {
    const name = (document.getElementById("name") as HTMLInputElement | null)?.value.trim() || "";
    const email = (document.getElementById("email") as HTMLInputElement | null)?.value.trim() || "";
    const message = (document.getElementById("message") as HTMLTextAreaElement | null)?.value.trim() || "";
    if (!name) {
      alert("Por favor, ingresa tu nombre antes de enviar por WhatsApp.");
      document.getElementById("name")?.focus();
      return;
    }
    if (!message) {
      alert("Por favor, ingresa tu consulta antes de enviar por WhatsApp.");
      document.getElementById("message")?.focus();
      return;
    }
    const text = encodeURIComponent(
      `Consulta Aliara IT\nNombre: ${name}\nEmail: ${email || "-"}\nMensaje: ${message}`
    );
    window.open(`https://wa.me/${site.phoneWa}?text=${text}`, "_blank");
  }

  return (
    <form
      id="contactForm"
      className={`php-email-form needs-validation w-100${validated ? " was-validated" : ""}`}
      action="/api/contact"
      method="POST"
      noValidate
      onSubmit={onSubmit}
    >
      <div className="row">
        <div className="col-md-6 form-group mb-3">
          <label htmlFor="name" className="form-label">Nombre:</label>
          <input type="text" name="name" className="form-control" id="name" required />
          <div className="invalid-feedback">Por favor, ingresa tu nombre.</div>
        </div>
        <div className="col-md-6 form-group mb-3">
          <label htmlFor="email" className="form-label">Email:</label>
          <input type="email" className="form-control" name="email" id="email" required />
          <div className="invalid-feedback">Por favor, ingresa un email válido.</div>
        </div>
      </div>
      <div className="form-group mb-3">
        <label htmlFor="message" className="form-label">Mensaje:</label>
        <textarea className="form-control" name="message" id="message" rows={4} required />
        <div className="invalid-feedback">Por favor, ingresa tu mensaje.</div>
      </div>
      <div className="d-flex gap-2">
        <button type="submit" className="btn btn-primary w-100" disabled={sending}>
          {label}
        </button>
        <button type="button" id="whatsappBtn" className="btn btn-success" title={`Enviar la consulta por WhatsApp a ${site.phoneDisplay}`} onClick={sendWhatsapp}>
          <img
            src="https://upload.wikimedia.org/wikipedia/commons/archive/6/6b/20181018120917%21WhatsApp.svg"
            alt="WhatsApp"
            width={24}
            height={24}
          />
          <span>WhatsApp</span>
        </button>
      </div>
    </form>
  );
}
