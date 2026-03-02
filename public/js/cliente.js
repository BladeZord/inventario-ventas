import { loading, confirmarModal, PAGE_SIZE_DEFAULT, renderPagination } from "./utilidades.js";

const API_BASE = `${window.location.origin}/api`;

async function cargarClientesList() {
  loading(true);
  try {
    const res = await fetch(`${API_BASE}/cliente`, { cache: "no-store" });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const payload = await res.json();
    if (payload.codigo !== 200) return [];
    return Array.isArray(payload.data) ? payload.data : [];
  } finally {
    loading(false);
  }
}

const obtenerClientePorId = async function (id) {
  loading(true);
  try {
    const res = await fetch(`${API_BASE}/cliente/${id}`, { cache: "no-store" });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const payload = await res.json();
    if (payload.codigo !== 200) return null;
    return payload.data;
  } finally {
    loading(false);
  }
};

const crearCliente = async function (cliente) {
  loading(true);
  try {
    const res = await fetch(`${API_BASE}/cliente`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(cliente),
    });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const payload = await res.json();
    if (payload.codigo !== 200) return null;
    return payload.data;
  } finally {
    loading(false);
  }
};

const actualizarCliente = async function (cliente) {
  loading(true);
  try {
    const res = await fetch(`${API_BASE}/cliente/${cliente.id}`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(cliente),
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

const eliminarCliente = async function (id) {
  loading(true);
  try {
    const res = await fetch(`${API_BASE}/cliente/${id}`, { method: "DELETE" });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const payload = await res.json();
    if (payload.codigo !== 200) return null;
    return payload.data;
  } finally {
    loading(false);
  }
};

function renderClientes(clientes, tbody, showEmpty = true) {
  if (!Array.isArray(clientes) || clientes.length === 0) {
    tbody.innerHTML = showEmpty ? `<tr><td colspan="8" class="text-center">No hay clientes disponibles</td></tr>` : "";
    return;
  }
  tbody.innerHTML = clientes
    .map(
      (c) => `
    <tr>
      <td>${c.id ?? ""}</td>
      <td>${c.identificacion ?? ""}</td>
      <td>${c.nombres ?? ""}</td>
      <td>${c.apellidos ?? ""}</td>
      <td>${c.correo ?? ""}</td>
      <td>${c.telefono ?? ""}</td>
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

function openModalCliente(accion, cliente, onSaveSuccess = null) {
  const prev = document.getElementById("modalCliente");
  if (prev) prev.remove();

  const modalWrap = document.createElement("div");
  modalWrap.id = "modalCliente";
  modalWrap.innerHTML = `
    <div class="modal fade" id="exampleModalCliente" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">${accion} Cliente</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form id="formCliente">
              <div class="row">
                <div class="col-md-6 mb-2">
                  <label class="col-form-label">Identificación:</label>
                  <input type="text" class="form-control" id="identificacion" name="identificacion" required>
                </div>
                <div class="col-md-6 mb-2">
                  <label class="col-form-label">Nombres:</label>
                  <input type="text" class="form-control" id="nombres" name="nombres" required>
                </div>
                <div class="col-md-6 mb-2">
                  <label class="col-form-label">Apellidos:</label>
                  <input type="text" class="form-control" id="apellidos" name="apellidos">
                </div>
                <div class="col-md-6 mb-2">
                  <label class="col-form-label">Correo:</label>
                  <input type="email" class="form-control" id="correo" name="correo">
                </div>
                <div class="col-md-6 mb-2">
                  <label class="col-form-label">Teléfono:</label>
                  <input type="text" class="form-control" id="telefono" name="telefono">
                </div>
                <div class="col-md-6 mb-2">
                  <label class="col-form-label">Estado:</label>
                  <select class="form-control" id="estado" name="estado">
                    <option value="ACTIVO">ACTIVO</option>
                    <option value="INACTIVO">INACTIVO</option>
                  </select>
                </div>
                <div class="col-12 mb-2">
                  <label class="col-form-label">Dirección:</label>
                  <input type="text" class="form-control" id="direccion" name="direccion">
                </div>
              </div>
              <input type="hidden" id="id" name="id">
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-primary" id="btnGuardarCliente">Guardar</button>
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

  if (accion === "Editar" && cliente) {
    setVal("id", cliente.id);
    setVal("identificacion", cliente.identificacion);
    setVal("nombres", cliente.nombres);
    setVal("apellidos", cliente.apellidos ?? "");
    setVal("correo", cliente.correo ?? "");
    setVal("telefono", cliente.telefono ?? "");
    setVal("direccion", cliente.direccion ?? "");
    setVal("estado", cliente.estado ?? "ACTIVO");
  } else {
    setVal("id", "");
    setVal("estado", "ACTIVO");
  }

  const modalEl = document.getElementById("exampleModalCliente");
  const bsModal = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true });
  bsModal.show();

  modalEl.addEventListener("hidden.bs.modal", () => modalWrap.remove());

  const tbody = document.getElementById("data-clientes");

  document.getElementById("btnGuardarCliente").onclick = async () => {
    const form = document.getElementById("formCliente");
    if (!form.reportValidity()) return;
    const data = Object.fromEntries(new FormData(form).entries());
    const payload = {
      identificacion: data.identificacion.trim(),
      nombres: data.nombres.trim(),
      apellidos: data.apellidos?.trim() || "",
      correo: data.correo?.trim() || null,
      telefono: data.telefono?.trim() || null,
      direccion: data.direccion?.trim() || null,
      estado: data.estado || "ACTIVO",
    };
    if (accion === "Editar" && data.id) {
      payload.id = Number(data.id);
      const ok = await actualizarCliente(payload);
      if (ok != null) { bsModal.hide(); if (onSaveSuccess) await onSaveSuccess(); else await cargarClientesList().then((list) => renderClientes(list, tbody)); }
    } else {
      const ok = await crearCliente(payload);
      if (ok != null) { bsModal.hide(); if (onSaveSuccess) await onSaveSuccess(); else await cargarClientesList().then((list) => renderClientes(list, tbody)); }
    }
  };
}

const PAGE_SIZE = PAGE_SIZE_DEFAULT;

export async function init() {
  const tbody = document.getElementById("data-clientes");
  if (!tbody) return;

  let listCompleta = [];
  let currentPage = 1;

  function refreshTable() {
    const start = (currentPage - 1) * PAGE_SIZE;
    const slice = listCompleta.slice(start, start + PAGE_SIZE);
    renderClientes(slice.length ? slice : [], tbody, listCompleta.length === 0);
    renderPagination("pagination-clientes", listCompleta.length, PAGE_SIZE, currentPage, (p) => {
      currentPage = p;
      refreshTable();
    });
  }

  async function refreshFromServer() {
    try {
      listCompleta = await cargarClientesList();
      currentPage = 1;
      refreshTable();
    } catch (e) {
      console.error(e);
      tbody.innerHTML = `<tr><td colspan="8" class="text-center text-danger">Error cargando clientes</td></tr>`;
    }
  }

  await refreshFromServer();

  const btnNuevo = document.getElementById("btnNuevoCliente");
  if (btnNuevo) btnNuevo.onclick = () => openModalCliente("Nuevo", null, refreshFromServer);

  tbody.addEventListener("click", async (e) => {
    const btn = e.target.closest("button[data-action]");
    if (!btn) return;
    const id = btn.dataset.id;
    const action = btn.dataset.action;
    if (action === "editar") {
      const cliente = await obtenerClientePorId(id);
      if (cliente) openModalCliente("Editar", cliente, refreshFromServer);
    }
    if (action === "eliminar") {
      const confirmado = await confirmarModal("¿Eliminar este cliente?", "Eliminar cliente");
      if (!confirmado) return;
      const ok = await eliminarCliente(id);
      if (ok != null) await refreshFromServer();
    }
  });
}
