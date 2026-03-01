/**
 * Muestra u oculta un overlay de loading (sin borrar el contenido de la página).
 * @param {boolean} ejecutar - true para mostrar, false para ocultar
 */
export const loading = (ejecutar) => {
  const ID = "app-loading-overlay";
  const existente = document.getElementById(ID);

  if (ejecutar) {
    // Si ya existe, no lo vuelvas a crear
    if (existente) return true;

    const overlay = document.createElement("div");
    overlay.id = ID;
    overlay.className =
      "position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-25";
    overlay.style.zIndex = "9999";

    overlay.innerHTML = `
        <div class="spinner-border" role="status" aria-label="Cargando">
          <span class="visually-hidden">Cargando...</span>
        </div>
      `;

    document.body.appendChild(overlay);
    return true;
  }

  // ocultar
  if (existente) existente.remove();
  return false;
};

/**
 * Muestra un mensaje centrado tipo overlay.
 * @param {string} mensaje - texto a mostrar
 * @param {"success"|"danger"|"warning"|"info"|"primary"|"secondary"|"light"|"dark"} tipo
 * @param {{timeout?: number, closable?: boolean}} opts
 */
export function mostrarMensaje(mensaje, tipo = "info", opts = {}) {
  const { timeout = 2500, closable = true } = opts;

  const ID = "app-mensaje-overlay";
  const existente = document.getElementById(ID);
  if (existente) existente.remove();

  const overlay = document.createElement("div");
  overlay.id = ID;
  overlay.className =
    "position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-25";
  overlay.style.zIndex = "9999";

  // Caja del mensaje (sin innerHTML para el mensaje)
  const alertBox = document.createElement("div");
  alertBox.className = `alert alert-${tipo} shadow m-0`;
  alertBox.setAttribute("role", "alert");
  alertBox.style.maxWidth = "520px";
  alertBox.style.width = "90%";

  const row = document.createElement("div");
  row.className = "d-flex align-items-start gap-2";

  const text = document.createElement("div");
  text.className = "flex-grow-1";
  text.textContent = String(mensaje ?? "");

  row.appendChild(text);

  if (closable) {
    const btn = document.createElement("button");
    btn.type = "button";
    btn.className = "btn-close";
    btn.setAttribute("aria-label", "Cerrar");
    btn.addEventListener("click", () => overlay.remove());
    row.appendChild(btn);
  }

  alertBox.appendChild(row);
  overlay.appendChild(alertBox);

  // Cerrar al hacer click fuera del alert
  overlay.addEventListener("click", (e) => {
    if (e.target === overlay) overlay.remove();
  });

  document.body.appendChild(overlay);

  // Auto-cierre
  if (timeout && timeout > 0) {
    setTimeout(() => {
      const el = document.getElementById(ID);
      if (el) el.remove();
    }, timeout);
  }

  return overlay; // por si quieres manipularlo
}

