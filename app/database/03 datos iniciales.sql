USE db_inventario_ventas;

-- =========================
-- 1) categorias (10)
-- =========================
INSERT INTO categorias (nombre, descripcion, estado) VALUES
('Frutas y verduras', 'Productos frescos de origen vegetal', 'ACTIVO'),
('Cárnicos', 'Carnes y embutidos', 'ACTIVO'),
('Lácteos', 'Leche, queso, yogurt y derivados', 'ACTIVO'),
('Panadería', 'Pan, pasteles y productos horneados', 'ACTIVO'),
('Bebidas', 'Jugos, gaseosas, agua y energizantes', 'ACTIVO'),
('Abarrotes', 'Productos de despensa y básicos', 'ACTIVO'),
('Limpieza', 'Productos de aseo del hogar', 'ACTIVO'),
('Cuidado personal', 'Higiene y cuidado personal', 'ACTIVO'),
('Congelados', 'Productos congelados', 'ACTIVO'),
('Dulcería', 'Golosinas y snacks', 'ACTIVO');

-- =========================
-- 2) unidades_medida (10)
-- =========================
INSERT INTO unidades_medida (nombre, descripcion, estado) VALUES
('Unidad', 'Venta por unidad', 'ACTIVO'),
('Kilogramo', 'Peso en kg', 'ACTIVO'),
('Gramo', 'Peso en g', 'ACTIVO'),
('Libra', 'Peso en lb', 'ACTIVO'),
('Litro', 'Volumen en L', 'ACTIVO'),
('Mililitro', 'Volumen en ml', 'ACTIVO'),
('Paquete', 'Venta por paquete', 'ACTIVO'),
('Caja', 'Venta por caja', 'ACTIVO'),
('Funda', 'Venta por funda', 'ACTIVO'),
('Docena', 'Venta por docena', 'ACTIVO');

-- =========================
-- 3) productos (10)
--    OJO: id_categoria 1..10, id_unidad_medida 1..10
-- =========================
INSERT INTO productos
(id_categoria, nombre, descripcion, precio_compra, precio_venta, stock, stock_minimo, id_unidad_medida, estado)
VALUES
(1, 'Banano', 'Banano maduro', 0.18, 0.25, 200, 30, 4, 'ACTIVO'),              -- libra
(1, 'Tomate riñón', 'Tomate para ensalada', 0.60, 0.85, 120, 20, 4, 'ACTIVO'),  -- libra
(2, 'Pechuga de pollo', 'Pechuga sin hueso', 2.20, 2.90, 80, 10, 4, 'ACTIVO'),  -- libra
(3, 'Leche entera 1L', 'Leche entera', 0.85, 1.10, 60, 12, 5, 'ACTIVO'),        -- litro
(3, 'Queso fresco', 'Queso fresco para mesa', 2.80, 3.50, 40, 8, 4, 'ACTIVO'),  -- libra
(4, 'Pan de molde', 'Pan tajado', 1.00, 1.35, 50, 10, 7, 'ACTIVO'),             -- paquete
(5, 'Agua 600ml', 'Agua sin gas', 0.20, 0.35, 300, 50, 6, 'ACTIVO'),            -- mililitro
(6, 'Arroz 1kg', 'Arroz grano largo', 0.95, 1.20, 90, 15, 2, 'ACTIVO'),         -- kg
(7, 'Detergente 500g', 'Detergente en polvo', 0.80, 1.10, 70, 10, 3, 'ACTIVO'), -- gramos
(10, 'Chocolate barra', 'Chocolate 50g', 0.35, 0.55, 150, 25, 1, 'ACTIVO');     -- unidad

-- =========================
-- 4) clientes (10)
--    identificacion UNIQUE
-- =========================
INSERT INTO clientes (identificacion, nombres, apellidos, correo, telefono, direccion, estado) VALUES
('0900000001', 'Carlos', 'Pérez', 'carlos.perez@mail.com', '0991111111', 'Guayaquil', 'ACTIVO'),
('0900000002', 'María', 'Gómez', 'maria.gomez@mail.com', '0992222222', 'Guayaquil', 'ACTIVO'),
('0900000003', 'José', 'Vera', 'jose.vera@mail.com', '0993333333', 'Durán', 'ACTIVO'),
('0900000004', 'Ana', 'Torres', 'ana.torres@mail.com', '0994444444', 'Samborondón', 'ACTIVO'),
('0900000005', 'Luis', 'Mendoza', 'luis.mendoza@mail.com', '0995555555', 'Milagro', 'ACTIVO'),
('0900000006', 'Sofía', 'Cedeño', 'sofia.cedeno@mail.com', '0996666666', 'Guayaquil', 'ACTIVO'),
('0900000007', 'Diego', 'Ortega', 'diego.ortega@mail.com', '0997777777', 'Daule', 'ACTIVO'),
('0900000008', 'Valeria', 'Rojas', 'valeria.rojas@mail.com', '0998888888', 'Guayaquil', 'ACTIVO'),
('0900000009', 'Pedro', 'Salazar', 'pedro.salazar@mail.com', '0989999999', 'Durán', 'ACTIVO'),
('0900000010', 'Camila', 'Morales', 'camila.morales@mail.com', '0981010101', 'Samborondón', 'ACTIVO'),
('0900000000', 'Consumidor', 'final', null, null, null, 'ACTIVO');

-- =========================
-- 5) ventas (10)
--    numero_factura UNIQUE
-- =========================
INSERT INTO ventas
(id_cliente, numero_factura, subtotal, descuento, impuesto, total, metodo_pago, estado)
VALUES
(1, 'FAC-000001', 5.40, 0.00, 0.00, 5.40, 'EFECTIVO', 'ACTIVO'),
(2, 'FAC-000002', 8.25, 0.50, 0.00, 7.75, 'TARJETA', 'ACTIVO'),
(3, 'FAC-000003', 3.10, 0.00, 0.00, 3.10, 'EFECTIVO', 'ACTIVO'),
(4, 'FAC-000004', 12.60, 1.00, 0.00, 11.60, 'TRANSFERENCIA', 'ACTIVO'),
(5, 'FAC-000005', 6.80, 0.30, 0.00, 6.50, 'EFECTIVO', 'ACTIVO'),
(6, 'FAC-000006', 9.90, 0.00, 0.00, 9.90, 'TARJETA', 'ACTIVO'),
(7, 'FAC-000007', 4.70, 0.20, 0.00, 4.50, 'EFECTIVO', 'ACTIVO'),
(8, 'FAC-000008', 15.00, 1.50, 0.00, 13.50, 'TRANSFERENCIA', 'ACTIVO'),
(9, 'FAC-000009', 2.60, 0.00, 0.00, 2.60, 'EFECTIVO', 'ACTIVO'),
(10,'FAC-000010', 7.40, 0.40, 0.00, 7.00, 'TARJETA', 'ACTIVO');

-- =========================
-- 6) detalle_ventas (10)
--    Importante: id_venta 1..10, id_producto 1..10
--    total_linea = (cantidad * precio_unitario) - descuento
-- =========================
INSERT INTO detalle_ventas
(id_venta, id_producto, cantidad, precio_unitario, descuento, total_linea, estado)
VALUES
(1,  1, 10, 0.25, 0.00, 2.50, 'ACTIVO'), -- Banano
(1,  7,  2, 0.35, 0.00, 0.70, 'ACTIVO'), -- Agua 600ml
(2,  4,  3, 1.10, 0.00, 3.30, 'ACTIVO'), -- Leche 1L
(2, 10,  5, 0.55, 0.50, 2.25, 'ACTIVO'), -- Chocolate (descuento)
(3,  8,  2, 1.20, 0.00, 2.40, 'ACTIVO'), -- Arroz 1kg
(4,  3,  4, 2.90, 1.00, 10.60,'ACTIVO'), -- Pechuga (desc)
(5,  6,  5, 1.35, 0.30, 6.45, 'ACTIVO'), -- Pan
(6,  5,  2, 3.50, 0.00, 7.00, 'ACTIVO'), -- Queso
(7,  2,  6, 0.85, 0.20, 4.90, 'ACTIVO'), -- Tomate
(8,  9, 10, 1.10, 1.50, 9.50, 'ACTIVO'); -- Detergente