<?php
  $title = $title ?? 'Inventario';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($title) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <!-- Overlay lo crea JS cuando haga falta; no lo dejes fijo vacío -->
  <div id="layout-container"></div>

  <!-- OJO: usa rutas absolutas desde /public -->
  <script type="module" src="/js/app.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>