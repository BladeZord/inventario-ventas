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

/** Tamaño de página por defecto para listados */
export const PAGE_SIZE_DEFAULT = 10;

/**
 * Renderiza controles de paginación (Bootstrap) en el contenedor indicado.
 * @param {string} containerId - ID del elemento donde se insertará la paginación
 * @param {number} totalItems - Total de registros
 * @param {number} pageSize - Registros por página
 * @param {number} currentPage - Página actual (1-based)
 * @param {(page: number) => void} onPageChange - Callback al cambiar de página
 */
export function renderPagination(containerId, totalItems, pageSize, currentPage, onPageChange) {
  const container = document.getElementById(containerId);
  if (!container) return;
  if (totalItems <= 0) {
    container.innerHTML = "";
    return;
  }
  const totalPages = Math.max(1, Math.ceil(totalItems / pageSize));
  const page = Math.max(1, Math.min(currentPage, totalPages));
  // Mostrar siempre la barra cuando hay datos (aunque sea 1 página)
  let html = '<nav aria-label="Paginación" class="d-flex align-items-center flex-wrap gap-2">';
  html += `<span class="small text-muted">Página ${page} de ${totalPages}</span>`;
  html += '<ul class="pagination pagination-sm mb-0">';
  html += `<li class="page-item ${page <= 1 ? "disabled" : ""}"><a class="page-link" href="#" data-page="${page - 1}">Anterior</a></li>`;
  for (let i = 1; i <= totalPages; i++) {
    if (totalPages > 7 && (i > 2 && i < totalPages - 1) && Math.abs(i - page) > 1) {
      if (i === 3 && page > 4) html += '<li class="page-item disabled"><span class="page-link">…</span></li>';
      continue;
    }
    if (totalPages > 7 && i === 3 && page <= 4) html += '<li class="page-item disabled"><span class="page-link">…</span></li>';
    html += `<li class="page-item ${i === page ? "active" : ""}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
  }
  if (totalPages > 7 && page < totalPages - 1) html += '<li class="page-item disabled"><span class="page-link">…</span></li>';
  html += `<li class="page-item ${page >= totalPages ? "disabled" : ""}"><a class="page-link" href="#" data-page="${page + 1}">Siguiente</a></li>`;
  html += "</ul></nav>";
  container.innerHTML = html;
  container.querySelectorAll(".page-link[data-page]").forEach((el) => {
    el.addEventListener("click", (e) => {
      e.preventDefault();
      if (el.closest(".page-item.disabled")) return;
      const p = Number(el.dataset.page);
      if (p >= 1 && p <= totalPages) onPageChange(p);
    });
  });
}

