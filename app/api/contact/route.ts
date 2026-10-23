import { NextResponse } from "next/server";
import { crearConsulta } from "@/lib/consultas";

export const runtime = "nodejs";
export const dynamic = "force-dynamic";

async function readConsulta(request: Request) {
  const contentType = request.headers.get("content-type") || "";
  if (contentType.includes("application/json")) {
    const body = (await request.json()) as {
      name?: string;
      nombre?: string;
      email?: string;
      message?: string;
      mensaje?: string;
    };
    return {
      nombre: String(body.name || body.nombre || "").trim(),
      email: String(body.email || "").trim(),
      mensaje: String(body.message || body.mensaje || "").trim(),
    };
  }
  const form = await request.formData();
  return {
    nombre: String(form.get("name") || form.get("nombre") || "").trim(),
    email: String(form.get("email") || "").trim(),
    mensaje: String(form.get("message") || form.get("mensaje") || "").trim(),
  };
}

export async function POST(request: Request) {
  try {
    const consulta = await readConsulta(request);
    if (!consulta.nombre || !consulta.email || !consulta.mensaje) {
      return NextResponse.json({ error: "Completá nombre, email y mensaje." }, { status: 400 });
    }
    await crearConsulta(consulta);
    return NextResponse.json({ ok: true });
  } catch (error) {
    const message = error instanceof Error ? error.message : "No se pudo enviar la consulta";
    return NextResponse.json({ error: message }, { status: 502 });
  }
}
