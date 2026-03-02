import { loading, confirmarModal, PAGE_SIZE_DEFAULT, renderPagination } from "./utilidades.js";

const API_BASE = `${window.location.origin}/api`;

async function cargarCategoriasList() {
  loading(true);
  try {
    const res = await fetch(`${API_BASE}/categoria`, { cache: "no-store" });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const payload = await res.json();
    if (payload.codigo !== 200) return [];
    return Array.isArray(payload.data) ? payload.data : [];
  } finally {
    loading(false);
  }
}

const obtenerCategoriaPorId = async function (id) {
  loading(true);
  try {
    const res = await fetch(`${API_BASE}/categoria/${id}`, { cache: "no-store" });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const payload = await res.json();
    if (payload.codigo !== 200) return null;
    return payload.data;
  } finally {
    loading(false);
  }
};

const crearCategoria = async function (categoria) {
  loading(true);
  try {
    const res = await fetch(`${API_BASE}/categoria`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(categoria),
    });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const payload = await res.json();
    if (payload.codigo !== 200) return null;
    return payload.data;
  } finally {
    loading(false);
  }
};

const actualizarCategoria = async function (categoria) {
  loading(true);
  try {
    const res = await fetch(`${API_BASE}/categoria/${categoria.id}`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(categoria),
    });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const payload = await res.json();
    if (payload.codigo !== 200) return null;
    return payload.data;
  } catch (e) {
    console.error(e);
    return null;
  } finally {
    loading(false);
  }
};

const eliminarCategoria = async function (id) {
  loading(true);
  try {
    const res = await fetch(`${API_BASE}/categoria/${id}`, { method: "DELETE" });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const payload = await res.json();
    if (payload.codigo !== 200) return null;
    return payload.data;
  } finally {
    loading(false);
  }
};

function renderCategorias(categorias, tbody, showEmpty = true) {
  if (!Array.isArray(categorias) || categorias.length === 0) {
    tbody.innerHTML = showEmpty ? `<tr><td colspan="5" class="text-center">No hay categorías disponibles</td></tr>` : "";
    return;
  }

  tbody.innerHTML = categorias
    .map(
      (c) => `
    <tr>
      <td>${c.id ?? ""}</td>
      <td>${c.nombre ?? ""}</td>
      <td>${c.descripcion ?? ""}</td>
      <td>${c.estado ?? ""}</td>
      <td>
        <button class="btn btn-sm btn-outline-primary" data-action="editar" data-id="${c.id}">Editar</button>
        <button class="btn btn-sm btn-outline-danger" data-action="eliminar" data-id="${c.id}">Eliminar</button>
      </td>
    </tr>
  `
    )
    .join("");
}

function openModalCategoria(accion, categoria, onSaveSuccess = null) {
  const prev = document.getElementById("modalCategoria");
  if (prev) prev.remove();

  const modalWrap = document.createElement("div");
  modalWrap.id = "modalCategoria";
  modalWrap.innerHTML = `
    <div class="modal fade" id="exampleModalCategoria" tabindex="-1" aria-labelledby="exampleModalCategoriaLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalCategoriaLabel">${accion} Categoría</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <form id="formCategoria">
              <div class="form-group mb-2">
                <label class="col-form-label">Nombre:</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
              </div>

              <div class="form-group mb-2">
                <label class="col-form-label">Descripción:</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="2"></textarea>
              </div>

              <div class="form-group mb-2">
                <label class="col-form-label">Estado:</label>
                <select class="form-control" id="estado" name="estado">
                  <option value="ACTIVO">ACTIVO</option>
                  <option value="INACTIVO">INACTIVO</option>
                </select>
              </div>

              <input type="hidden" id="id" name="id">
            </form>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-primary" id="btnGuardarCategoria">Guardar</button>
          </div>
        </div>
      </div>
    </div>
  `;

  document.body.appendChild(modalWrap);

  const setVal = (id, val) => {
    const el = document.getElementById(id);
    if (el) el.value = val ?? "";
  };

  if (accion === "Editar" && categoria) {
    setVal("id", categoria.id);
    setVal("nombre", categoria.nombre);
    setVal("descripcion", categoria.descripcion ?? "");
    setVal("estado", categoria.estado ?? "ACTIVO");
  } else {
    setVal("id", "");
    setVal("nombre", "");
    setVal("descripcion", "");
    setVal("estado", "ACTIVO");
  }

  const modalEl = document.getElementById("exampleModalCategoria");
  const bsModal = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true });
  bsModal.show();

  modalEl.addEventListener("hidden.bs.modal", () => {
    modalWrap.remove();
  });

  const tbody = document.getElementById("data-categorias");

  document.getElementById("btnGuardarCategoria").onclick = async () => {
    const form = document.getElementById("formCategoria");
    if (!form.reportValidity()) return;

    const data = Object.fromEntries(new FormData(form).entries());
    const payload = {
      nombre: data.nombre.trim(),
      descripcion: data.descripcion?.trim() || null,
      estado: data.estado || "ACTIVO",
    };
    if (accion === "Editar" && data.id) {
      payload.id = Number(data.id);
      const ok = await actualizarCategoria(payload);
      if (ok != null) {
        bsModal.hide();
        if (onSaveSuccess) await onSaveSuccess(); else await cargarCategoriasList().then((list) => renderCategorias(list, tbody));
      }
    } else {
      const ok = await crearCategoria(payload);
      if (ok != null) {
        bsModal.hide();
        if (onSaveSuccess) await onSaveSuccess(); else await cargarCategoriasList().then((list) => renderCategorias(list, tbody));
      }
    }
  };
}

function cargarModalFormularioCategoria(btn, accion, categoria = null, onSaveSuccess = null) {
  if (!btn) return;
  btn.onclick = () => openModalCategoria(accion, categoria, onSaveSuccess);
}

export async function init() {
  const tbody = document.getElementById("data-categorias");
  if (!tbody) {
    console.error("No se encontró #data-categorias.");
    return;
  }

  const PAGE_SIZE = PAGE_SIZE_DEFAULT;
  let listCompleta = [];
  let currentPage = 1;

  function refreshTable() {
    const start = (currentPage - 1) * PAGE_SIZE;
    const slice = listCompleta.slice(start, start + PAGE_SIZE);
    renderCategorias(slice.length ? slice : [], tbody, listCompleta.length === 0);
    renderPagination("pagination-categorias", listCompleta.length, PAGE_SIZE, currentPage, (p) => {
      currentPage = p;
      refreshTable();
    });
  }

  async function refreshFromServer() {
    try {
      listCompleta = await cargarCategoriasList();
      currentPage = 1;
      refreshTable();
    } catch (e) {
      console.error(e);
      tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Error cargando categorías</td></tr>`;
    }
  }

  await refreshFromServer();

  const btnNuevo = document.getElementById("btnNuevoCategoria");
  if (btnNuevo) cargarModalFormularioCategoria(btnNuevo, "Nuevo", null, refreshFromServer);

  tbody.addEventListener("click", async (e) => {
    const btn = e.target.closest("button[data-action]");
    if (!btn) return;
    const id = btn.dataset.id;
    const action = btn.dataset.action;
    if (action === "editar") {
      const categoria = await obtenerCategoriaPorId(id);
      if (categoria) openModalCategoria("Editar", categoria, refreshFromServer);
    }
    if (action === "eliminar") {
      const confirmado = await confirmarModal("¿Está seguro de eliminar esta categoría?", "Eliminar categoría");
      if (!confirmado) return;
      const ok = await eliminarCategoria(id);
      if (ok != null) await refreshFromServer();
    }
  });
}
