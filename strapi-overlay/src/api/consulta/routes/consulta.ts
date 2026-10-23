import { factories } from "@strapi/strapi";

export default factories.createCoreRouter("api::consulta.consulta", {
  config: {
    create: {
      auth: false,
    },
  },
});
