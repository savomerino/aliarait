import { site } from "@/lib/site";

export type ConsultaInput = {
  nombre: string;
  email: string;
  mensaje: string;
};

export async function crearConsulta(input: ConsultaInput) {
  const base = process.env.STRAPI_URL || "http://127.0.0.1:1347";
  const response = await fetch(`${base}/api/consultas`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
    },
    body: JSON.stringify({
      data: {
        nombre: input.nombre,
        email: input.email,
        mensaje: input.mensaje,
        destino: site.email,
      },
    }),
  });

  const data = await response.json().catch(() => ({}));
  if (!response.ok) {
    const message =
      data?.error?.message ||
      data?.error ||
      "No se pudo registrar la consulta";
    throw new Error(typeof message === "string" ? message : "No se pudo registrar la consulta");
  }
  return data;
}
