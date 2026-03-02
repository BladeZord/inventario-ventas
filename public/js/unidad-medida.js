import { loading, confirmarModal, PAGE_SIZE_DEFAULT, renderPagination } from "./utilidades.js";

const API_BASE = `${window.location.origin}/api`;

async function cargarUnidadesMedidaList() {
  loading(true);
  try {
    const res = await fetch(`${API_BASE}/unidad-medida`, { cache: "no-store" });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const payload = await res.json();
    if (payload.codigo !== 200) return [];
    return Array.isArray(payload.data) ? payload.data : [];
  } finally {
    loading(false);
  }
}

const obtenerUnidadMedidaPorId = async function (id) {
  loading(true);
  try {
    const res = await fetch(`${API_BASE}/unidad-medida/${id}`, { cache: "no-store" });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const payload = await res.json();
    if (payload.codigo !== 200) return null;
    return payload.data;
  } finally {
    loading(false);
  }
};

const crearUnidadMedida = async function (unidad) {
  loading(true);
  try {
    const res = await fetch(`${API_BASE}/unidad-medida`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(unidad),
    });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const payload = await res.json();
    if (payload.codigo !== 200) return null;
    return payload.data;
  } finally {
    loading(false);
  }
};

const actualizarUnidadMedida = async function (unidad) {
  loading(true);
  try {
    const res = await fetch(`${API_BASE}/unidad-medida/${unidad.id}`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(unidad),
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

const eliminarUnidadMedida = async function (id) {
  loading(true);
  try {
    const res = await fetch(`${API_BASE}/unidad-medida/${id}`, { method: "DELETE" });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const payload = await res.json();
    if (payload.codigo !== 200) return null;
    return payload.data;
  } finally {
    loading(false);
  }
};

function renderUnidadesMedida(lista, tbody, showEmpty = true) {
  if (!Array.isArray(lista) || lista.length === 0) {
    tbody.innerHTML = showEmpty ? `<tr><td colspan="5" class="text-center">No hay unidades de medida disponibles</td></tr>` : "";
    return;
  }

  tbody.innerHTML = lista
    .map(
      (u) => `
    <tr>
      <td>${u.id ?? ""}</td>
      <td>${u.nombre ?? ""}</td>
      <td>${u.descripcion ?? ""}</td>
      <td>${u.estado ?? ""}</td>
      <td>
        <button class="btn btn-sm btn-outline-primary" data-action="editar" data-id="${u.id}">Editar</button>
        <button class="btn btn-sm btn-outline-danger" data-action="eliminar" data-id="${u.id}">Eliminar</button>
      </td>
    </tr>
  `
    )
    .join("");
}

function openModalUnidadMedida(accion, unidad, onSaveSuccess = null) {
  const prev = document.getElementById("modalUnidadMedida");
  if (prev) prev.remove();

  const modalWrap = document.createElement("div");
  modalWrap.id = "modalUnidadMedida";
  modalWrap.innerHTML = `
    <div class="modal fade" id="exampleModalUnidadMedida" tabindex="-1" aria-labelledby="exampleModalUnidadMedidaLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalUnidadMedidaLabel">${accion} Unidad de medida</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <form id="formUnidadMedida">
              <div class="form-group mb-2">
                <label class="col-form-label">Nombre:</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required placeholder="Ej: Libra, Unidad">
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
            <button type="button" class="btn btn-primary" id="btnGuardarUnidadMedida">Guardar</button>
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

  if (accion === "Editar" && unidad) {
    setVal("id", unidad.id);
    setVal("nombre", unidad.nombre);
    setVal("descripcion", unidad.descripcion ?? "");
    setVal("estado", unidad.estado ?? "ACTIVO");
  } else {
    setVal("id", "");
    setVal("nombre", "");
    setVal("descripcion", "");
    setVal("estado", "ACTIVO");
  }

  const modalEl = document.getElementById("exampleModalUnidadMedida");
  const bsModal = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true });
  bsModal.show();

  modalEl.addEventListener("hidden.bs.modal", () => {
    modalWrap.remove();
  });

  const tbody = document.getElementById("data-unidades-medida");

  document.getElementById("btnGuardarUnidadMedida").onclick = async () => {
    const form = document.getElementById("formUnidadMedida");
    if (!form.reportValidity()) return;

    const data = Object.fromEntries(new FormData(form).entries());
    const payload = {
      nombre: data.nombre.trim(),
      descripcion: data.descripcion?.trim() || null,
      estado: data.estado || "ACTIVO",
    };
    if (accion === "Editar" && data.id) {
      payload.id = Number(data.id);
      const ok = await actualizarUnidadMedida(payload);
      if (ok != null) {
        bsModal.hide();
        if (onSaveSuccess) await onSaveSuccess(); else await cargarUnidadesMedidaList().then((list) => renderUnidadesMedida(list, tbody));
      }
    } else {
      const ok = await crearUnidadMedida(payload);
      if (ok != null) {
        bsModal.hide();
        if (onSaveSuccess) await onSaveSuccess(); else await cargarUnidadesMedidaList().then((list) => renderUnidadesMedida(list, tbody));
      }
    }
  };
}

function cargarModalFormularioUnidadMedida(btn, accion, unidad = null, onSaveSuccess = null) {
  if (!btn) return;
  btn.onclick = () => openModalUnidadMedida(accion, unidad, onSaveSuccess);
}

const PAGE_SIZE = PAGE_SIZE_DEFAULT;

export async function init() {
  const tbody = document.getElementById("data-unidades-medida");
  if (!tbody) return;

  let listCompleta = [];
  let currentPage = 1;

  function refreshTable() {
    const start = (currentPage - 1) * PAGE_SIZE;
    const slice = listCompleta.slice(start, start + PAGE_SIZE);
    renderUnidadesMedida(slice.length ? slice : [], tbody, listCompleta.length === 0);
    renderPagination("pagination-unidades-medida", listCompleta.length, PAGE_SIZE, currentPage, (p) => {
      currentPage = p;
      refreshTable();
    });
  }

  async function refreshFromServer() {
    try {
      listCompleta = await cargarUnidadesMedidaList();
      currentPage = 1;
      refreshTable();
    } catch (e) {
      console.error(e);
      tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Error cargando unidades de medida</td></tr>`;
    }
  }

  await refreshFromServer();

  const btnNuevo = document.getElementById("btnNuevoUnidadMedida");
  if (btnNuevo) cargarModalFormularioUnidadMedida(btnNuevo, "Nuevo", null, refreshFromServer);

  tbody.addEventListener("click", async (e) => {
    const btn = e.target.closest("button[data-action]");
    if (!btn) return;
    const id = btn.dataset.id;
    const action = btn.dataset.action;
    if (action === "editar") {
      const unidad = await obtenerUnidadMedidaPorId(id);
      if (unidad) openModalUnidadMedida("Editar", unidad, refreshFromServer);
    }
    if (action === "eliminar") {
      const confirmado = await confirmarModal("¿Está seguro de eliminar esta unidad de medida?", "Eliminar unidad de medida");
      if (!confirmado) return;
      const ok = await eliminarUnidadMedida(id);
      if (ok != null) await refreshFromServer();
    }
  });
}
