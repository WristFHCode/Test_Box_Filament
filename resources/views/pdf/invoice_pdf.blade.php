<!DOCTYPE html>
<html>
<head>
    <title>Invoice #{{ $invoice->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
    </style>
</head>
<body>
    <h1>Invoice #{{ $invoice->id }}</h1>
    <p>Tanggal: {{ $invoice->created_at }}</p>
    <p>Subtotal: ${{ number_format($invoice->subtotal, 2) }}</p>
    <p>Pajak: {{ $invoice->taxes }}%</p>
    <p>Total: ${{ number_format($invoice->total, 2) }}</p>

    <h2>Produk:</h2>
    <ul>
        @foreach ($invoice->invoiceProducts as $product)
            <li>{{ $product->cardboardProduct->name }} - Qty: {{ $product->quantity }} - Total: ${{ number_format($product->total_price, 2) }}</li>
        @endforeach
    </ul>
</body>
</html>
