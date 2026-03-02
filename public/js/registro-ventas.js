import { loading, confirmarModal } from "./utilidades.js";

const API_BASE = `${window.location.origin}/api`;

let clientes = [];
let productos = [];

async function cargarClientes() {
  try {
    const res = await fetch(`${API_BASE}/cliente`, { cache: "no-store" });
    if (!res.ok) return [];
    const payload = await res.json();
    if (payload.codigo !== 200) return [];
    return Array.isArray(payload.data) ? payload.data : [];
  } catch (e) {
    console.error(e);
    return [];
  }
}

async function cargarProductos() {
  try {
    const res = await fetch(`${API_BASE}/producto`, { cache: "no-store" });
    if (!res.ok) return [];
    const payload = await res.json();
    if (payload.codigo !== 200) return [];
    return Array.isArray(payload.data) ? payload.data : [];
  } catch (e) {
    console.error(e);
    return [];
  }
}

async function cargarVentas() {
  loading(true);
  try {
    const res = await fetch(`${API_BASE}/venta`, { cache: "no-store" });
    if (!res.ok) return [];
    const payload = await res.json();
    if (payload.codigo !== 200) return [];
    return Array.isArray(payload.data) ? payload.data : [];
  } finally {
    loading(false);
  }
}

async function obtenerVentaPorId(id) {
  loading(true);
  try {
    const res = await fetch(`${API_BASE}/venta/${id}`, { cache: "no-store" });
    if (!res.ok) return null;
    const payload = await res.json();
    if (payload.codigo !== 200) return null;
    return payload.data;
  } finally {
    loading(false);
  }
}

async function crearVenta(data) {
  loading(true);
  try {
    const res = await fetch(`${API_BASE}/venta`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data),
    });
    if (!res.ok) return null;
    const payload = await res.json();
    if (payload.codigo !== 200) return null;
    return payload.data;
  } finally {
    loading(false);
  }
}

async function anularVenta(id) {
  loading(true);
  try {
    const res = await fetch(`${API_BASE}/venta/${id}/anular`, { method: "PUT" });
    if (!res.ok) return null;
    const payload = await res.json();
    if (payload.codigo !== 200) return null;
    return payload.data;
  } finally {
    loading(false);
  }
}

function renderSelectClientes(selectEl) {
  if (!selectEl) return;
  selectEl.innerHTML = '<option value="">Seleccione un cliente</option>' +
    clientes.map((c) => `<option value="${c.id}">${(c.nombres || "")} ${(c.apellidos || "").trim()} - ${c.identificacion || ""}</option>`).join("");
}

function getProductoOptions() {
  return productos.map((p) => `<option value="${p.id}" data-precio="${Number(p.precio_venta) || 0}">${p.nombre ?? p.id} - ${Number(p.precio_venta) || 0}</option>`).join("");
}

function addDetalleRow() {
  const tbody = document.getElementById("venta-detalle-tbody");
  if (!tbody) return;
  const tr = document.createElement("tr");
  tr.className = "detalle-row";
  tr.innerHTML = `
    <td>
      <select class="form-select form-select-sm producto-select">
        <option value="">Seleccione producto</option>
        ${getProductoOptions()}
      </select>
    </td>
    <td><input type="number" class="form-control form-control-sm cantidad-input" min="1" value="1"></td>
    <td><input type="number" class="form-control form-control-sm precio-input" min="0" step="0.01" value="0"></td>
    <td><input type="number" class="form-control form-control-sm descuento-input" min="0" step="0.01" value="0"></td>
    <td><span class="total-linea">0.00</span></td>
    <td><button type="button" class="btn btn-sm btn-outline-danger quitar-linea">×</button></td>
  `;
  tbody.appendChild(tr);

  const sel = tr.querySelector(".producto-select");
  const precioIn = tr.querySelector(".precio-input");
  sel.addEventListener("change", () => {
    const opt = sel.selectedOptions[0];
    if (opt && opt.value) {
      const p = Number(opt.dataset.precio) || 0;
      precioIn.value = p;
      updateLineTotal(tr);
    }
  });

  tr.querySelector(".cantidad-input").addEventListener("input", () => updateLineTotal(tr));
  tr.querySelector(".precio-input").addEventListener("input", () => updateLineTotal(tr));
  tr.querySelector(".descuento-input").addEventListener("input", () => updateLineTotal(tr));

  tr.querySelector(".quitar-linea").addEventListener("click", () => {
    tr.remove();
    updateTotales();
  });
}

function updateLineTotal(tr) {
  const cantidad = Number(tr.querySelector(".cantidad-input").value) || 0;
  const precio = Number(tr.querySelector(".precio-input").value) || 0;
  const descuento = Number(tr.querySelector(".descuento-input").value) || 0;
  const totalLinea = Math.max(0, cantidad * precio - descuento);
  tr.querySelector(".total-linea").textContent = totalLinea.toFixed(2);
  updateTotales();
}

function updateTotales() {
  const tbody = document.getElementById("venta-detalle-tbody");
  if (!tbody) return;
  let subtotal = 0;
  tbody.querySelectorAll(".detalle-row").forEach((tr) => {
    const t = tr.querySelector(".total-linea");
    if (t) subtotal += Number(t.textContent) || 0;
  });
  const descuentoGlobal = Number(document.getElementById("venta-descuento")?.value) || 0;
  const impuesto = Number(document.getElementById("venta-impuesto")?.value) || 0;
  const total = Math.max(0, subtotal - descuentoGlobal + impuesto);

  const stEl = document.getElementById("venta-subtotal");
  const totEl = document.getElementById("venta-total");
  if (stEl) stEl.textContent = subtotal.toFixed(2);
  if (totEl) totEl.textContent = total.toFixed(2);
}

function getDetallesFromForm() {
  const tbody = document.getElementById("venta-detalle-tbody");
  if (!tbody) return [];
  const detalles = [];
  tbody.querySelectorAll(".detalle-row").forEach((tr) => {
    const idProducto = tr.querySelector(".producto-select")?.value;
    if (!idProducto) return;
    const cantidad = Number(tr.querySelector(".cantidad-input")?.value) || 0;
    const precioUnitario = Number(tr.querySelector(".precio-input")?.value) || 0;
    const descuento = Number(tr.querySelector(".descuento-input")?.value) || 0;
    const totalLinea = Number(tr.querySelector(".total-linea")?.textContent) || 0;
    if (cantidad > 0)
      detalles.push({ id_producto: Number(idProducto), cantidad, precio_unitario: precioUnitario, descuento, total_linea: totalLinea });
  });
  return detalles;
}

function renderVentas(ventas, tbody) {
  if (!tbody) return;
  if (!Array.isArray(ventas) || ventas.length === 0) {
    tbody.innerHTML = '<tr><td colspan="7" class="text-center">No hay ventas registradas</td></tr>';
    return;
  }
  tbody.innerHTML = ventas
    .map(
      (v) => `
    <tr>
      <td>${v.id ?? ""}</td>
      <td>${v.numero_factura ?? ""}</td>
      <td>${v.cliente_nombre ?? (v.cliente_nombres || "") + " " + (v.cliente_apellidos || "")}</td>
      <td>${Number(v.total ?? 0).toFixed(2)}</td>
      <td>${v.fecha_creacion ?? ""}</td>
      <td><span class="badge ${v.estado === "ACTIVO" ? "bg-success" : "bg-secondary"}">${v.estado ?? ""}</span></td>
      <td>
        <button class="btn btn-sm btn-outline-primary btn-ver-venta" data-id="${v.id}">Ver</button>
        ${v.estado === "ACTIVO" ? `<button class="btn btn-sm btn-outline-danger btn-anular-venta" data-id="${v.id}">Anular</button>` : ""}
      </td>
    </tr>
  `
    )
    .join("");
}

function openModalVerVenta(venta) {
  const prev = document.getElementById("modalVerVenta");
  if (prev) prev.remove();

  const modalWrap = document.createElement("div");
  modalWrap.id = "modalVerVenta";
  const detallesRows = (venta.detalles || [])
    .map(
      (d) => `
    <tr>
      <td>${d.producto_nombre ?? d.id_producto}</td>
      <td>${d.cantidad ?? ""}</td>
      <td>${Number(d.precio_unitario ?? 0).toFixed(2)}</td>
      <td>${Number(d.descuento ?? 0).toFixed(2)}</td>
      <td>${Number(d.total_linea ?? 0).toFixed(2)}</td>
    </tr>
  `
    )
    .join("");

  modalWrap.innerHTML = `
    <div class="modal fade" id="exampleModalVerVenta" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Venta #${venta.id ?? ""} - ${venta.numero_factura ?? ""}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <p><strong>Cliente:</strong> ${venta.cliente_nombre ?? ""}</p>
            <p><strong>Subtotal:</strong> ${Number(venta.subtotal ?? 0).toFixed(2)} | <strong>Descuento:</strong> ${Number(venta.descuento ?? 0).toFixed(2)} | <strong>Impuesto:</strong> ${Number(venta.impuesto ?? 0).toFixed(2)} | <strong>Total:</strong> ${Number(venta.total ?? 0).toFixed(2)}</p>
            <p><strong>Método de pago:</strong> ${venta.metodo_pago ?? "-"} | <strong>Estado:</strong> ${venta.estado ?? ""}</p>
            <table class="table table-sm">
              <thead><tr><th>Producto</th><th>Cant.</th><th>P. unit.</th><th>Desc.</th><th>Total</th></tr></thead>
              <tbody>${detallesRows}</tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  `;

  document.body.appendChild(modalWrap);
  const modalEl = document.getElementById("exampleModalVerVenta");
  const bsModal = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true });
  bsModal.show();
  modalEl.addEventListener("hidden.bs.modal", () => modalWrap.remove());
}

export async function init() {
  loading(true);
  try {
    [clientes, productos] = await Promise.all([cargarClientes(), cargarProductos()]);
  } finally {
    loading(false);
  }

  renderSelectClientes(document.getElementById("venta-cliente"));

  const tbodyDetalle = document.getElementById("venta-detalle-tbody");
  if (tbodyDetalle) tbodyDetalle.innerHTML = "";
  addDetalleRow();

  document.getElementById("venta-agregar-linea")?.addEventListener("click", () => addDetalleRow());

  document.getElementById("venta-descuento")?.addEventListener("input", updateTotales);
  document.getElementById("venta-impuesto")?.addEventListener("input", updateTotales);

  document.getElementById("venta-registrar")?.addEventListener("click", async () => {
    const idCliente = document.getElementById("venta-cliente")?.value;
    const numeroFactura = document.getElementById("venta-numero_factura")?.value?.trim();
    if (!idCliente || !numeroFactura) {
      alert("Seleccione un cliente e ingrese el número de factura.");
      return;
    }
    const detalles = getDetallesFromForm();
    if (detalles.length === 0) {
      alert("Agregue al menos un producto al detalle.");
      return;
    }
    const subtotal = Array.from(document.querySelectorAll("#venta-detalle-tbody .total-linea")).reduce((s, el) => s + (Number(el.textContent) || 0), 0);
    const descuento = Number(document.getElementById("venta-descuento")?.value) || 0;
    const impuesto = Number(document.getElementById("venta-impuesto")?.value) || 0;
    const total = Math.max(0, subtotal - descuento + impuesto);

    const payload = {
      id_cliente: Number(idCliente),
      numero_factura: numeroFactura,
      subtotal,
      descuento,
      impuesto,
      total,
      metodo_pago: document.getElementById("venta-metodo_pago")?.value || null,
      estado: "ACTIVO",
      detalles,
    };
    const ok = await crearVenta(payload);
    if (ok != null) {
      document.getElementById("venta-numero_factura").value = "";
      document.getElementById("venta-cliente").value = "";
      document.getElementById("venta-descuento").value = "0";
      document.getElementById("venta-impuesto").value = "0";
      tbodyDetalle.innerHTML = "";
      addDetalleRow();
      updateTotales();
      const ventas = await cargarVentas();
      renderVentas(ventas, document.getElementById("data-ventas"));
    }
  });

  const dataVentas = document.getElementById("data-ventas");
  const refreshVentas = () => cargarVentas().then((list) => renderVentas(list, dataVentas));
  await refreshVentas();

  dataVentas?.addEventListener("click", async (e) => {
    const btnVer = e.target.closest(".btn-ver-venta");
    const btnAnular = e.target.closest(".btn-anular-venta");
    if (btnVer) {
      const id = btnVer.dataset.id;
      const venta = await obtenerVentaPorId(id);
      if (venta) openModalVerVenta(venta);
    }
    if (btnAnular) {
      const id = btnAnular.dataset.id;
      const confirmado = await confirmarModal("¿Está seguro de anular esta venta?", "Anular venta");
      if (!confirmado) return;
      const ok = await anularVenta(id);
      if (ok != null) await refreshVentas();
    }
  });
}
