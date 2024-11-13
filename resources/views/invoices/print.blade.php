<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .invoice-details {
            margin-top: 20px;
        }
        .invoice-details th, .invoice-details td {
            padding: 8px;
            text-align: left;
        }
        .invoice-details th {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>

    <div class="invoice-header">
        <h1>Invoice #{{ $invoice->id }}</h1>
        <p>Tanggal: {{ $invoice->created_at->format('d/m/Y') }}</p>
    </div>

    <table class="invoice-details" width="100%" border="1">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Harga per Unit</th>
                <th>Jumlah</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->invoiceProducts as $product)
                <tr>
                    <td>{{ $product->cardboardProduct->name }}</td>
                    <td>${{ number_format($product->price_per_unit, 2) }}</td>
                    <td>{{ $product->quantity }}</td>
                    <td>${{ number_format($product->total_price, 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3" style="text-align: right;"><strong>Subtotal:</strong></td>
                <td>${{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right;"><strong>Pajak ({{ $invoice->taxes }}%):</strong></td>
                <td>${{ number_format($invoice->subtotal * $invoice->taxes / 100, 2) }}</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                <td>${{ number_format($invoice->total, 2) }}</td>
            </tr>
        </tbody>
    </table>

</body>
</html>
