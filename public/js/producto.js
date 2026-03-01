import { loading } from "./utilidades.js";

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

function renderProductos(productos, tbody) {
  if (!Array.isArray(productos) || productos.length === 0) {
    tbody.innerHTML = `<tr><td colspan="10" class="text-center">No hay productos disponibles</td></tr>`;
    return;
  }

  tbody.innerHTML = productos.map(p => `
    <tr>
      <td>${p.id ?? ""}</td>
      <td>${p.categoria ?? ""}</td>
      <td>${p.codigo ?? ""}</td>
      <td>${p.nombre ?? ""}</td>
      <td>${p.precio_compra ?? ""}</td>
      <td>${p.precio_venta ?? ""}</td>
      <td>${p.stock ?? p.stock_actual ?? ""}</td>
      <td>${p.stock_minimo ?? ""}</td>
      <td>${p.unidad_medida ?? ""}</td>
      <td>
        <button class="btn btn-sm btn-outline-primary" data-action="editar" data-id="${p.id}">Editar</button>
        <button class="btn btn-sm btn-outline-danger" data-action="eliminar" data-id="${p.id}">Eliminar</button>
      </td>
    </tr>
  `).join("");
}


function openModalProducto(accion, producto, categorias) {
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
                  <label class="col-form-label">Código:</label>
                  <input type="text" class="form-control" id="codigo" name="codigo" required>
                </div>

                <div class="form-group mb-2">
                  <label class="col-form-label">Nombre:</label>
                  <input type="text" class="form-control" id="nombre" name="nombre" required>
                </div>

                <div class="form-group mb-2">
                  <label class="col-form-label">Precio de compra:</label>
                  <input type="number" class="form-control" id="precio_compra" name="precio_compra" step="0.01" min="0" required>
                </div>

                <div class="form-group mb-2">
                  <label class="col-form-label">Precio de venta:</label>
                  <input type="number" class="form-control" id="precio_venta" name="precio_venta" step="0.01" min="0" required>
                </div>

                <div class="form-group mb-2">
                  <label class="col-form-label">Stock actual:</label>
                  <input type="number" class="form-control" id="stock_actual" name="stock_actual" step="1" min="0" required>
                </div>

                <div class="form-group mb-2">
                  <label class="col-form-label">Stock mínimo:</label>
                  <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" step="1" min="0" required>
                </div>

                <div class="form-group mb-2">
                  <label class="col-form-label">Unidad de medida:</label>
                  <input type="text" class="form-control" id="unidad_medida" name="unidad_medida">
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
    setVal("codigo", producto.codigo);
    setVal("nombre", producto.nombre);
    setVal("precio_compra", producto.precio_compra);
    setVal("precio_venta", producto.precio_venta);
    setVal("stock_actual", producto.stock ?? producto.stock_actual);
    setVal("stock_minimo", producto.stock_minimo);
    setVal("unidad_medida", producto.unidad_medida ?? "");
    setVal("estado", producto.estado ?? "ACTIVO");
  } else {
    setVal("id", "");
    setVal("estado", "ACTIVO");
  }

  const modalEl = document.getElementById("exampleModalProducto");
  const bsModal = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true });
  bsModal.show();

  modalEl.addEventListener("hidden.bs.modal", () => {
    modalWrap.remove();
  });

  const tbody = document.getElementById("data-productos");
  const refreshTable = () => cargarProductos().then((list) => renderProductos(list, tbody));

  document.getElementById("btnGuardarProducto").onclick = async () => {
    const form = document.getElementById("formProducto");
    if (!form.reportValidity()) return;

    const data = Object.fromEntries(new FormData(form).entries());
    const payload = {
      id_categoria: data.categoria_id ? Number(data.categoria_id) : null,
      codigo: data.codigo,
      nombre: data.nombre,
      precio_compra: Number(data.precio_compra),
      precio_venta: Number(data.precio_venta),
      stock: Number(data.stock_actual),
      stock_minimo: Number(data.stock_minimo),
      unidad_medida: data.unidad_medida || "UNIDAD",
      estado: data.estado || "ACTIVO",
    };
    if (accion === "Editar" && data.id) {
      payload.id = Number(data.id);
      const ok = await actualizarProducto(payload);
      if (ok != null) {
        bsModal.hide();
        await refreshTable();
      }
    } else {
      const ok = await crearProducto(payload);
      if (ok != null) {
        bsModal.hide();
        await refreshTable();
      }
    }
  };
}

function cargarModalFormulario(btn, accion, producto = null, categorias = []) {
  if (!btn) return;
  btn.onclick = () => openModalProducto(accion, producto, categorias);
}


export async function init() {
  const tbody = document.getElementById("data-productos");
  if (!tbody) {
    console.error("No se encontró #data-productos.");
    return;
  }

  const categorias = await cargarCategorias();

  try {
    const productos = await cargarProductos();
    renderProductos(productos, tbody);
  } catch (e) {
    console.error(e);
    tbody.innerHTML = `<tr><td colspan="10" class="text-center text-danger">Error cargando productos</td></tr>`;
  }

  const btnNuevo = document.getElementById("btnNuevo");
  if (btnNuevo) {
    cargarModalFormulario(btnNuevo, "Nuevo", null, categorias);
  }

  const refreshTable = () => cargarProductos().then((list) => renderProductos(list, tbody));

  tbody.addEventListener("click", async (e) => {
    const btn = e.target.closest("button[data-action]");
    if (!btn) return;

    const id = btn.dataset.id;
    const action = btn.dataset.action;

    if (action === "editar") {
      const producto = await obtenerPoductoPorId(id);
      if (producto) openModalProducto("Editar", producto, categorias);
    }

    if (action === "eliminar") {
      if (!confirm("¿Eliminar este producto?")) return;
      const ok = await eliminarProducto(id);
      if (ok != null) await refreshTable();
    }
  });
}