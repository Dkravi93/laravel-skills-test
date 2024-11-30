<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <h2>Product Entry Form</h2>
    <form id="product-form" class="mb-4">
        @csrf
        <input type="hidden" id="editId" name="id">
        <div class="mb-3">
            <label for="productName" class="form-label">Product Name</label>
            <input type="text" class="form-control" id="productName" name="productName" required>
        </div>
        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity in Stock</label>
            <input type="number" class="form-control" id="quantity" name="quantity" required>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Price per Item</label>
            <input type="number" class="form-control" id="price" name="price" required>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    <h3>Submitted Data</h3>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Product Name</th>
            <th>Quantity in Stock</th>
            <th>Price per Item</th>
            <th>Datetime Submitted</th>
            <th>Total Value</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody id="data-table">
        <!-- Data will be dynamically loaded here -->
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function () {
        const fetchData = () => {
            $.ajax({
                url: "{{ route('products.data') }}",
                method: 'GET',
                success: function (response) {
                    const { data } = response;
                    let totalSum = 0;
                    const rows = data.map(entry => {
                        totalSum += entry.totalValue;
                        return `
                            <tr data-id="${entry.id}">
                                <td>${entry.productName}</td>
                                <td>${entry.quantity}</td>
                                <td>${entry.price}</td>
                                <td>${entry.datetime}</td>
                                <td>${entry.totalValue.toFixed(2)}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning edit-btn">Edit</button>
                                    <button class="btn btn-sm btn-danger delete-btn">Delete</button>
                                </td>
                            </tr>`;
                    });
                    rows.push(` 
                        <tr>
                            <td colspan="4"><strong>Total</strong></td>
                            <td colspan="2"><strong>${totalSum.toFixed(2)}</strong></td>
                        </tr>`);
                    $('#data-table').html(rows.join(''));
                }
            });
        };

        $('#product-form').submit(function (e) {
            e.preventDefault();
            const formData = $(this).serialize();
            $.ajax({
                url: $('#editId').val() ? "{{ route('products.update') }}" : "{{ route('products.store') }}",
                method: 'POST',
                data: formData,
                success: function () {
                    $('#product-form')[0].reset();
                    $('#editId').val('');
                    fetchData();
                }
            });
        });

        // Edit Button Handler
        $('#data-table').on('click', '.edit-btn', function () {
            const row = $(this).closest('tr');
            const id = row.data('id');
            const productName = row.find('td:nth-child(1)').text();
            const quantity = row.find('td:nth-child(2)').text();
            const price = row.find('td:nth-child(3)').text();

            $('#editId').val(id);
            $('#productName').val(productName);
            $('#quantity').val(quantity);
            $('#price').val(price);
        });

        // Delete Button Handler
        $('#data-table').on('click', '.delete-btn', function () {
            const id = $(this).closest('tr').data('id');
            if (confirm('Are you sure you want to delete this product?')) {
                $.ajax({
                    url: "{{ route('products.delete') }}",
                    method: 'POST',
                    data: { id, _token: '{{ csrf_token() }}' },
                    success: function () {
                        fetchData();
                    }
                });
            }
        });

        fetchData();
    });
</script>
</body>
</html>
