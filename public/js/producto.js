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


const crearProducto = async function (producto) {
  loading(true);
  try {
    const res = await fetch(`${API_BASE}/producto`, { cache: "no-store" });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const payload = await res.json();
    if (payload.codigo !== 200) return null;
    return payload.data;
  }
  finally {
    loading(false);
  }
}

const actualizarProducto = async function (producto) {
  loading(true);
  try {
    const res = await fetch(`${API_BASE}/producto/${producto.id}`, { cache: "no-store" });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const payload = await res.json();
    if (payload.codigo !== 200) return null;
    return payload.data;
  } catch (e) {
    console.error(e);
    return null;
  }
}

const eliminarProducto = async function (id) {
  loading(true);
  try {
    const res = await fetch(`${API_BASE}/producto/${id}`, { cache: "no-store" });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const payload = await res.json();
    if (payload.codigo !== 200) return null;
    return payload.data;
  }
  finally {
    loading(false);
  }
}

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
      <td>${p.stock_actual ?? ""}</td>
      <td>${p.stock_minimo ?? ""}</td>
      <td>${p.unidad_medida ?? ""}</td>
      <td>
        <button class="btn btn-sm btn-outline-primary" data-action="editar" data-id="${p.id}">Editar</button>
        <button class="btn btn-sm btn-outline-danger" data-action="eliminar" data-id="${p.id}">Eliminar</button>
      </td>
    </tr>
  `).join("");
}


function cargarModalFormulario(accion, producto = null) {
  const btnAgregar = document.getElementById("btnNuevo");
  btnAgregar.addEventListener("click", () => {
    if (accion === "Editar") {

    }

  })

  const modal = document.createElement("div");
  modal.innerHTML = 
  `
  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">New message</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
          <div class="form-group">
            <label for="recipient-name" class="col-form-label">Recipient:</label>
            <input type="text" class="form-control" id="recipient-name">
          </div>
          <div class="form-group">
            <label for="message-text" class="col-form-label">Message:</label>
            <textarea class="form-control" id="message-text"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Send message</button>
      </div>
    </div>
  </div>
</div>
  `;
  document.body.appendChild(modal);
  modal.addEventListener("click", (e) => {
    if (e.target === modal) modal.remove();
  });
  return modal;
}


export async function init() {
  // ✅ aquí es donde se toca el DOM (ya está la vista cargada)
  const tbody = document.getElementById("data-productos");
  if (!tbody) {
    console.error("No se encontró #data-productos. ¿La vista se inyectó bien?");
    return;
  }

  // ✅ “onInit”: cargar datos apenas entra a la vista
  try {
    const productos = await cargarProductos();
    console.log(productos);
    renderProductos(productos, tbody);
  } catch (e) {
    console.error(e);
    tbody.innerHTML = `<tr><td colspan="10" class="text-center text-danger">Error cargando productos</td></tr>`;
  }

  // ✅ eventos (delegación) para botones Editar/Eliminar dentro de la tabla
  tbody.addEventListener("click", async (e) => {
    const btn = e.target.closest("button[data-action]");
    if (!btn) return;

    const id = btn.dataset.id;
    const action = btn.dataset.action;

    if (action === "editar") {
      console.log("Editar", id);
      // aquí llamas obtenerProductoPorId(id) y abres modal
    }

    if (action === "eliminar") {
      console.log("Eliminar", id);
      // aquí llamas eliminarProducto(id) y recargas
    }
  });
}