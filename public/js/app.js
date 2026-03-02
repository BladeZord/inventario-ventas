
import { loading } from "./utilidades.js";

const routes = {
  "/": "/views/home/home.html",
  "/productos": "/views/producto/productos.html",
  "/categorias": "/views/categoria/categoria.html",
  "/unidades-medidas": "/views/unidad-medida/unidades-medida.html",
  "/registro-ventas": "/views/registro-ventas/registro-ventas.html",
};

let currentModule = null;

async function loadView(path) {
  const viewUrl = routes[path] || "/views/404/404.html";

  loading(true);
  try {
    const res = await fetch(viewUrl, { cache: "no-store" });
    if (!res.ok) throw new Error(`No se pudo cargar ${viewUrl} (${res.status})`);
    const html = await res.text();

    const container = document.getElementById("layout-container");
    container.innerHTML = html;

    const root = container.querySelector("[data-controller]");
    if (root?.dataset?.controller) {
      // Import dinámico del módulo controller
      currentModule = await import(root.dataset.controller);

      if (typeof currentModule.init === "function") {
        await currentModule.init();
      } else {
        console.warn("El controller no exporta init():", root.dataset.controller);
      }
    }
  } finally {
    loading(false);
  }
}

function navigate(path) {
  history.pushState({}, "", path);
  loadView(path);
}


window.addEventListener("popstate", () => loadView(location.pathname));

document.addEventListener("click", (e) => {
  const a = e.target.closest("a[data-link]");
  if (!a) return;
  e.preventDefault();
  navigate(a.getAttribute("href"));
});

loadView(location.pathname);
