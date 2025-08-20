<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>CRUD con PHP, MySQL, Bootstrap y Ajax</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="Estilos/Styles.css">
</head>
<body>
<div class="container mt-5">
  <h2 class="mb-4">Gestión de Productos</h2>
  <button class="btn btn-primary mb-3" id="btnNuevo">Nuevo Producto</button>

  <input type="text" id="filtroManual" class="form-control mb-3" placeholder="Filtrar por nombre">

  <table id="tablaProductos" class="table table-striped" style="width:100%">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Precio</th>
        <th>Stock</th>
        <th>Imagen</th>
        <th>Acciones</th>
      </tr>
    </thead>
  </table>
</div>

<div class="modal fade" id="modalProducto" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formProducto" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Producto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="id">
          <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" name="nombre" id="nombre" required>
          </div>
          <div class="mb-3">
            <label for="precio" class="form-label">Precio</label>
            <input type="number" step="0.01" min="0" class="form-control" name="precio" id="precio" required>
          </div>
          <div class="mb-3">
            <label for="stock" class="form-label">Stock</label>
            <input type="number" min="0" class="form-control" name="stock" id="stock" required>
          </div>
          <div class="mb-3">
            <label for="imagen" class="form-label">Imagen</label>
            <input type="file" class="form-control" name="imagen" id="imagen" accept="image/*">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Guardar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
  let tabla = $('#tablaProductos').DataTable({
    ajax: {
      url: 'api.php',
      type: 'GET',
      dataSrc: ''
    },
    columns: [
      { data: 'id' },
      { data: 'nombre' },
      { data: 'precio' },
      { data: 'stock' },
      {
        data: 'imagen',
        render: function(data) {
          return data ? `<img src="${data}" width="60">` : 'Sin imagen';
        }
      },
      {
        data: null,
        render: function(data) {
          return `
            <button class="btn btn-warning btn-sm btnEditar" data-id="${data.id}">Editar</button>
            <button class="btn btn-danger btn-sm btnEliminar" data-id="${data.id}">Eliminar</button>
          `;
        }
      }
    ]
  });

  $('#btnNuevo').click(function() {
    $('#formProducto')[0].reset();
    $('#id').val('');
    $('#modalProducto').modal('show');
  });

  $('#tablaProductos').on('click', '.btnEditar', function() {
    let id = $(this).data('id');
    $.get('api.php?id=' + id, function(data) {
      $('#id').val(data.id);
      $('#nombre').val(data.nombre);
      $('#precio').val(data.precio);
      $('#stock').val(data.stock);
      $('#modalProducto').modal('show');
    }, 'json');
  });

  $('#tablaProductos').on('click', '.btnEliminar', function() {
    if (confirm('¿Seguro que quieres eliminar este producto?')) {
      let id = $(this).data('id');
      $.ajax({
        url: 'api.php',
        type: 'DELETE',
        data: JSON.stringify({ id }),
        success: function() {
          tabla.ajax.reload();
        }
      });
    }
  });

  $('#formProducto').submit(function(e) {
    e.preventDefault();
    let formData = new FormData(this);

    let precio = parseFloat($('#precio').val());
    let stock = parseInt($('#stock').val());

    if (precio < 0 || stock < 0) {
      alert('No se permiten valores negativos');
      return;
    }

    let accion = $('#id').val() ? 'editar' : 'crear';

    $.ajax({
      url: 'api.php?accion=' + accion,
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function() {
        $('#modalProducto').modal('hide');
        tabla.ajax.reload();
      }
    });
  });

  $('#filtroManual').on('input', function() {
    let valor = $(this).val().toLowerCase();
    tabla.rows().every(function() {
      let nombre = this.data().nombre.toLowerCase();
      this.node().style.display = nombre.includes(valor) ? '' : 'none';
    });
  });
});
</script>
</body>
</html>