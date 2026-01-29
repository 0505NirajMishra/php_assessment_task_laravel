<!DOCTYPE html>
<html>
<head>
<title>Laravel CRUD AJAX</title>

<meta name="csrf-token" content="{{ csrf_token() }}">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</head>
<body class="container mt-4">

<button class="btn btn-primary mb-3" onclick="openModal()">Add Product</button>

<table id="productTable" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Name</th>
            <th>Price</th>
            <th>Description</th>
            <th>Images</th>
            <th>Action</th>
        </tr>
    </thead>
</table>

<!-- Modal -->
<div class="modal fade" id="productModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Add / Edit Product</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form id="productForm" enctype="multipart/form-data">
          <input type="hidden" id="product_id">

          <div class="mb-3">
            <label>Product Name</label>
            <input type="text" name="product_name" id="product_name" class="form-control">
          </div>

          <div class="mb-3">
            <label>Price</label>
            <input type="number" name="product_price" id="product_price" class="form-control">
          </div>

          <div class="mb-3">
            <label>Description</label>
            <textarea name="product_description" id="product_description" class="form-control"></textarea>
          </div>

          <div class="mb-3">
            <label>Images</label>
            <input type="file" name="product_image[]" class="form-control" multiple>
            <div id="oldImages" class="mt-2"></div>
          </div>

          <button type="submit" class="btn btn-success">Save</button>
        </form>
      </div>

    </div>
  </div>
</div>

<script>
let table;

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


$(document).ready(function () {
    table = $('#productTable').DataTable({
        ajax: {
            url: '/products/fetch',
            dataSrc: 'data'
        },
        columns: [
            { data: 'product_name' },
            { data: 'product_price' },
            { data: 'product_description' },
            {
                data: 'product_image',
                render: function (data) {
                    if (!data || data.length === 0) return 'No Image';
                    let img = '';
                    data.forEach(i => {
                        img += `<img src="/uploads/products/${i}" width="50" class="me-1">`;
                    });
                    return img;
                }
            },
            {
                data: 'id',
                render: function (id) {
                    return `
                        <button class="btn btn-sm btn-warning" onclick="edit(${id})">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteProduct(${id})">Delete</button>
                    `;
                }
            }
        ]
    });
});


function openModal() {
    $('#productForm')[0].reset();
    $('#product_id').val('');
    $('#oldImages').html('');
    new bootstrap.Modal(document.getElementById('productModal')).show();
}

$('#productForm').submit(function(e) {
    e.preventDefault();

    let id = $('#product_id').val();
    let url = id ? `/products/update/${id}` : `/products/store`;
    let formData = new FormData(this);

    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function() {
            bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
            table.ajax.reload();
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                let msg = '';
                Object.values(errors).forEach(e => msg += e[0] + '\n');
                alert(msg);
            }
        }
    });
});


function edit(id) {
    $.get(`/products/edit/${id}`, function(res) {
        $('#product_id').val(res.id);
        $('#product_name').val(res.product_name);
        $('#product_price').val(res.product_price);
        $('#product_description').val(res.product_description);

        let images = '';
        if (res.product_image) {
            res.product_image.forEach(img => {
                images += `<img src="/uploads/products/${img}" width="50" class="me-1">`;
            });
        }
        $('#oldImages').html(images);

        new bootstrap.Modal(document.getElementById('productModal')).show();
    });
}


function deleteProduct(id) {
    if (confirm('Delete this product?')) {
        $.ajax({
            url: `/products/delete/${id}`,
            type: 'DELETE',
            success: function() {
                table.ajax.reload();
            }
        });
    }
}
</script>

</body>
</html>
