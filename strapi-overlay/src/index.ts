import type { Core } from "@strapi/strapi";

const DESTINO = "info@aliarait.com";

type Consulta = {
  nombre?: string;
  email?: string;
  mensaje?: string;
};

export default {
  register() {},

  async bootstrap({ strapi }: { strapi: Core.Strapi }) {
    strapi.db.lifecycles.subscribe({
      models: ["api::consulta.consulta"],
      async afterCreate(event) {
        const result = event.result as Consulta | undefined;
        const nombre = result?.nombre || "Sin nombre";
        const email = result?.email || "";
        const mensaje = result?.mensaje || "";
        if (!process.env.SMTP_PASSWORD) {
          strapi.log.warn(
            `Consulta de ${email} guardada. El plugin de email de Strapi no tiene SMTP_PASSWORD y no pudo mandar el mail a ${DESTINO}`
          );
          return;
        }
        try {
          await strapi.plugin("email").service("email").send({
            to: DESTINO,
            replyTo: email || DESTINO,
            subject: `Consulta web de ${nombre}`,
            text: [
              "Llegó una consulta desde https://aliarait.com",
              "",
              `Nombre: ${nombre}`,
              `Email: ${email}`,
              "",
              mensaje,
            ].join("\n"),
          });
        } catch (error) {
          strapi.log.error(`El email de Strapi no pudo enviar a ${DESTINO}: ${error}`);
        }
      },
    });
  },
};
