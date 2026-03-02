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

/**
 * Muestra un modal de confirmación (Bootstrap) y devuelve una Promise con la respuesta.
 * @param {string} mensaje - Texto del mensaje (ej: "¿Eliminar este producto?")
 * @param {string} [titulo="Confirmar"] - Título del modal
 * @param {string} [textoConfirmar="Eliminar"] - Texto del botón de confirmar
 * @returns {Promise<boolean>} - true si confirma, false si cancela o cierra
 */
export function confirmarModal(mensaje, titulo = "Confirmar", textoConfirmar = "Eliminar") {
  return new Promise((resolve) => {
    const ID_WRAP = "app-modal-confirmar-wrap";
    const ID_MODAL = "app-modal-confirmar";
    const prev = document.getElementById(ID_WRAP);
    if (prev) prev.remove();

    const wrap = document.createElement("div");
    wrap.id = ID_WRAP;
    wrap.innerHTML = `
      <div class="modal fade" id="${ID_MODAL}" tabindex="-1" aria-labelledby="confirmarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="confirmarModalLabel">${titulo}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">${mensaje}</div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="button" class="btn btn-danger" id="app-modal-confirmar-ok">${textoConfirmar}</button>
            </div>
          </div>
        </div>
      </div>
    `;

    document.body.appendChild(wrap);
    const modalEl = document.getElementById(ID_MODAL);
    const bsModal = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true });

    const resolver = (valor) => {
      bsModal.hide();
      resolve(valor);
    };

    modalEl.addEventListener("hidden.bs.modal", () => {
      wrap.remove();
      resolve(false);
    });

    document.getElementById("app-modal-confirmar-ok").addEventListener("click", () => {
      resolver(true);
    });

    bsModal.show();
  });
}

