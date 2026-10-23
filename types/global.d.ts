export {};

declare global {
  interface Window {
    AOS?: { init: (options?: Record<string, unknown>) => void; refresh: () => void };
    bootstrap?: {
      Modal: {
        getOrCreateInstance: (el: Element) => { show: () => void; hide: () => void };
      };
    };
    html2canvas?: (
      element: HTMLElement,
      options?: Record<string, unknown>
    ) => Promise<HTMLCanvasElement>;
  }
}
