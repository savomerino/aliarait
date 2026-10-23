"use client";

import { useEffect, useRef } from "react";

const LINK_REWRITES: Array<[string, string]> = [
  ["AliaraIT_2025_AGO.html", "/rrss/2025/agosto"],
  ["AliaraIT_2025_SEPT.html", "/rrss/2025/septiembre"],
  ["index_2025_OCTUBRE.html", "/rrss/2025/octubre"],
];

function loadScript(src: string) {
  return new Promise<void>((resolve, reject) => {
    const existing = document.querySelector(`script[data-legacy-src="${src}"]`);
    if (existing) {
      resolve();
      return;
    }
    const script = document.createElement("script");
    script.src = src;
    script.async = false;
    script.dataset.legacySrc = src;
    script.onload = () => resolve();
    script.onerror = () => reject(new Error(src));
    document.body.appendChild(script);
  });
}

export default function LegacyHtmlApp({ src }: { src: string }) {
  const host = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const added: HTMLElement[] = [];
    let cancelled = false;

    async function mount() {
      const response = await fetch(src);
      let html = await response.text();
      for (const [from, to] of LINK_REWRITES) {
        html = html.replaceAll(from, to);
      }
      if (cancelled || !host.current) return;

      const doc = new DOMParser().parseFromString(html, "text/html");
      if (doc.title) document.title = doc.title;

      doc.head.querySelectorAll("style, link[rel='stylesheet'], link[rel='preconnect']").forEach((node) => {
        const clone = node.cloneNode(true) as HTMLElement;
        document.head.appendChild(clone);
        added.push(clone);
      });

      host.current.className = doc.body.className;
      host.current.innerHTML = doc.body.innerHTML;
      host.current.querySelectorAll("script").forEach((script) => script.remove());

      const external = [...doc.querySelectorAll("script")].filter((script) => script.src);
      const inline = [...doc.querySelectorAll("script")].filter((script) => !script.src);
      for (const script of external) {
        if (cancelled) return;
        await loadScript(script.src);
      }
      if (cancelled) return;

      for (const script of inline) {
        const el = document.createElement("script");
        el.textContent = script.textContent || "";
        document.body.appendChild(el);
        added.push(el);
      }
      document.dispatchEvent(new Event("DOMContentLoaded"));
    }

    mount().catch((error) => {
      if (host.current) host.current.textContent = `No se pudo abrir esta página: ${error.message}`;
    });

    return () => {
      cancelled = true;
      added.forEach((node) => node.remove());
      if (host.current) host.current.innerHTML = "";
    };
  }, [src]);

  return <div ref={host} />;
}
