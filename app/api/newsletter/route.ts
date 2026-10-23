import { NextResponse } from "next/server";
import { crearConsulta } from "@/lib/consultas";

export const runtime = "nodejs";
export const dynamic = "force-dynamic";

export async function POST(request: Request) {
  const form = await request.formData();
  const email = String(form.get("email") || "").trim();
  if (!email) {
    return NextResponse.json({ error: "Email requerido" }, { status: 400 });
  }
  try {
    await crearConsulta({
      nombre: "Suscripción",
      email,
      mensaje: `Nueva suscripción al boletín: ${email}`,
    });
    return NextResponse.json({ ok: true });
  } catch (error) {
    const message = error instanceof Error ? error.message : "No se pudo registrar la suscripción";
    return NextResponse.json({ error: message }, { status: 502 });
  }
}
