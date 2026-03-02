import { loading, confirmarModal, PAGE_SIZE_DEFAULT, renderPagination } from "./utilidades.js";

const API_BASE = `${window.location.origin}/api`;

async function cargarProductos() {
  loading(true);
  try {
    const res = await fetch(`${API_BASE}/producto`, { cache: "no-store" });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const payload = await res.json();
    if (payload.codigo !== 200) return [];
    return Array.isArray(payload.data) ? payload.data : [];
  } finally {
    loading(false);
  }
}

const obtenerPoductosPorCategoriaId = async function (id_categoria) {
  loading(true);
  try {
    const res = await fetch(`${API_BASE}/producto/categoria/${id_categoria}`, { cache: "no-store" });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);

    const payload = await res.json();

    // tu API usa { codigo, data }
    if (payload.codigo !== 200) return [];

    return Array.isArray(payload.data) ? payload.data : [];
  } finally {
    loading(false);
  }
}


const obtenerPoductoPorId = async function (id) {
  loading(true);
  try {
    const res = await fetch(`${API_BASE}/producto/${id}`, { cache: "no-store" });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);

    const payload = await res.json();

    if (payload.codigo !== 200) return null;

    return payload.data;
  } finally {
    loading(false);
  }
}

async function cargarCategorias() {
  try {
    const res = await fetch(`${API_BASE}/categoria`, { cache: "no-store" });
    if (!res.ok) return [];
    const payload = await res.json();
    if (payload.codigo !== 200) return [];
    return Array.isArray(payload.data) ? payload.data : [];
  } catch (e) {
    console.error(e);
    return [];
  }
}

const crearProducto = async function (producto) {
  loading(true);
  try {
    const res = await fetch(`${API_BASE}/producto`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(producto),
    });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const payload = await res.json();
    if (payload.codigo !== 200) return null;
    return payload.data;
  } finally {
    loading(false);
  }
};

const actualizarProducto = async function (producto) {
  loading(true);
  try {
    const res = await fetch(`${API_BASE}/producto/${producto.id}`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(producto),
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

const eliminarProducto = async function (id) {
  loading(true);
  try {
    const res = await fetch(`${API_BASE}/producto/${id}`, { method: "DELETE" });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const payload = await res.json();
    if (payload.codigo !== 200) return null;
    return payload.data;
  } finally {
    loading(false);
  }
};

function renderProductos(productos, tbody, showEmptyMessage = true) {
  if (!Array.isArray(productos) || productos.length === 0) {
    tbody.innerHTML = showEmptyMessage
      ? `<tr><td colspan="9" class="text-center">No hay productos disponibles</td></tr>`
      : "";
    return;
  }

  tbody.innerHTML = productos.map(p => `
    <tr>
      <td>${p.id ?? ""}</td>
      <td>${p.categoria_nombre ?? p.categoria ?? ""}</td>
      <td>${p.nombre ?? ""}</td>
      <td>${p.precio_compra ?? ""}</td>
      <td>${p.precio_venta ?? ""}</td>
      <td>${p.stock ?? p.stock_actual ?? ""}</td>
      <td>${p.stock_minimo ?? ""}</td>
      <td>${p.unidad_medida_nombre ?? p.unidad_medida ?? ""}</td>
      <td>
        <button class="btn btn-sm btn-outline-primary" data-action="editar" data-id="${p.id}">Editar</button>
        <button class="btn btn-sm btn-outline-danger" data-action="eliminar" data-id="${p.id}">Eliminar</button>
      </td>
    </tr>
  `).join("");
}


function openModalProducto(accion, producto, categorias, onSaveSuccess = null) {
  categorias = categorias || [];
  const prev = document.getElementById("modalProducto");
  if (prev) prev.remove();

  const modalWrap = document.createElement("div");
  modalWrap.id = "modalProducto";
  modalWrap.innerHTML = `
      <div class="modal fade" id="exampleModalProducto" tabindex="-1" aria-labelledby="exampleModalProductoLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalProductoLabel">${accion} Producto</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
              <form id="formProducto">
                <div class="form-group mb-2">
                  <label class="col-form-label">Categoría:</label>
                  <select class="form-control" id="categoria" name="categoria_id" required>
                    <option value="">Seleccione una categoría</option>
                    ${categorias.map(c => `<option value="${c.id}">${c.nombre}</option>`).join("")}
                  </select>
                </div>

                <div class="form-group mb-2">
                  <label class="col-form-label">Nombre:</label>
                  <input type="text" class="form-control" id="nombre" name="nombre" required placeholder="Nombre del producto">
                </div>

                <div class="form-group mb-2">
                  <label class="col-form-label">Precio de compra:</label>
                  <input type="number" class="form-control" id="precio_compra" name="precio_compra" step="0.01" min="0.01" required title="Debe ser mayor a 0">
                </div>

                <div class="form-group mb-2">
                  <label class="col-form-label">Precio de venta:</label>
                  <input type="number" class="form-control" id="precio_venta" name="precio_venta" step="0.01" min="0.01" required title="Debe ser mayor a 0">
                </div>

                <div class="form-group mb-2">
                  <label class="col-form-label">Stock actual:</label>
                  <input type="number" class="form-control" id="stock_actual" name="stock_actual" step="1" min="0" required title="No puede ser negativo">
                </div>

                <div class="form-group mb-2">
                  <label class="col-form-label">Stock mínimo:</label>
                  <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" step="1" min="0" required title="No puede ser negativo">
                </div>

                <div class="form-group mb-2">
                  <label class="col-form-label">Unidad de medida (ID):</label>
                  <input type="number" class="form-control" id="id_unidad_medida" name="id_unidad_medida" min="1" required title="ID de la unidad en unidades_medida">
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
              <button type="button" class="btn btn-primary" id="btnGuardarProducto">Guardar</button>
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

  if (accion === "Editar" && producto) {
    setVal("id", producto.id);
    setVal("categoria", producto.id_categoria ?? producto.categoria_id);
    setVal("nombre", producto.nombre);
    setVal("precio_compra", producto.precio_compra);
    setVal("precio_venta", producto.precio_venta);
    setVal("stock_actual", producto.stock ?? producto.stock_actual);
    setVal("stock_minimo", producto.stock_minimo);
    setVal("id_unidad_medida", producto.id_unidad_medida ?? "");
    setVal("estado", producto.estado ?? "ACTIVO");
  } else {
    setVal("id", "");
    setVal("id_unidad_medida", "1");
    setVal("estado", "ACTIVO");
  }

  const modalEl = document.getElementById("exampleModalProducto");
  const bsModal = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true });
  bsModal.show();

  modalEl.addEventListener("hidden.bs.modal", () => {
    modalWrap.remove();
  });

  const tbody = document.getElementById("data-productos");

  document.getElementById("btnGuardarProducto").onclick = async () => {
    const form = document.getElementById("formProducto");
    if (!form.reportValidity()) return;

    const data = Object.fromEntries(new FormData(form).entries());

    // Validaciones: nombre no vacío, precios > 0, stock >= 0 (no negativo)
    const nombre = (data.nombre || "").trim();
    if (!nombre) {
      alert("El nombre no puede estar vacío.");
      return;
    }
    const precioCompra = Number(data.precio_compra);
    const precioVenta = Number(data.precio_venta);
    const stockActual = Number(data.stock_actual);
    const stockMinimo = Number(data.stock_minimo);

    if (precioCompra <= 0) {
      alert("El precio de compra debe ser mayor a 0.");
      return;
    }
    if (precioVenta <= 0) {
      alert("El precio de venta debe ser mayor a 0.");
      return;
    }
    if (stockActual < 0) {
      alert("El stock actual no puede ser negativo.");
      return;
    }
    if (stockMinimo < 0) {
      alert("El stock mínimo no puede ser negativo.");
      return;
    }

    const payload = {
      id_categoria: data.categoria_id ? Number(data.categoria_id) : null,
      nombre,
      precio_compra: precioCompra,
      precio_venta: precioVenta,
      stock: stockActual,
      stock_minimo: stockMinimo,
      id_unidad_medida: Number(data.id_unidad_medida) || 1,
      estado: data.estado || "ACTIVO",
    };
    if (accion === "Editar" && data.id) {
      payload.id = Number(data.id);
      const ok = await actualizarProducto(payload);
      if (ok != null) {
        bsModal.hide();
        if (onSaveSuccess) await onSaveSuccess(); else await cargarProductos().then((list) => renderProductos(list, tbody));
      }
    } else {
      const ok = await crearProducto(payload);
      if (ok != null) {
        bsModal.hide();
        if (onSaveSuccess) await onSaveSuccess(); else await cargarProductos().then((list) => renderProductos(list, tbody));
      }
    }
  };
}

function cargarModalFormulario(btn, accion, producto = null, categorias = [], onSaveSuccess = null) {
  if (!btn) return;
  btn.onclick = () => openModalProducto(accion, producto, categorias, onSaveSuccess);
}


const PAGE_SIZE = PAGE_SIZE_DEFAULT;

export async function init() {
  const tbody = document.getElementById("data-productos");
  if (!tbody) {
    console.error("No se encontró #data-productos.");
    return;
  }

  const categorias = await cargarCategorias();
  let productosCompletos = [];
  let currentPage = 1;

  function refreshTable() {
    const start = (currentPage - 1) * PAGE_SIZE;
    const slice = productosCompletos.slice(start, start + PAGE_SIZE);
    renderProductos(slice.length ? slice : [], tbody, productosCompletos.length === 0);
    renderPagination("pagination-productos", productosCompletos.length, PAGE_SIZE, currentPage, (p) => {
      currentPage = p;
      refreshTable();
    });
  }

  async function refreshFromServer() {
    try {
      productosCompletos = await cargarProductos();
      currentPage = 1;
      refreshTable();
    } catch (e) {
      console.error(e);
      tbody.innerHTML = `<tr><td colspan="9" class="text-center text-danger">Error cargando productos</td></tr>`;
    }
  }

  await refreshFromServer();

  const btnNuevo = document.getElementById("btnNuevo");
  if (btnNuevo) {
    cargarModalFormulario(btnNuevo, "Nuevo", null, categorias, refreshFromServer);
  }

  tbody.addEventListener("click", async (e) => {
    const btn = e.target.closest("button[data-action]");
    if (!btn) return;

    const id = btn.dataset.id;
    const action = btn.dataset.action;

    if (action === "editar") {
      const producto = await obtenerPoductoPorId(id);
      if (producto) openModalProducto("Editar", producto, categorias, refreshFromServer);
    }

    if (action === "eliminar") {
      const confirmado = await confirmarModal("¿Está seguro de eliminar este producto?", "Eliminar producto");
      if (!confirmado) return;
      const ok = await eliminarProducto(id);
      if (ok != null) await refreshFromServer();
    }
  });
}