{{-- resources/views/invoices/pdf.blade.php --}}

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Factura No. {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .invoice-container { margin: 20px; padding: 20px; border: 1px solid #ccc; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; }
        .text-end { text-align: right; }
        .table-dark th { background-color: #f8f9fa; }
        .mb-4 { margin-bottom: 1.5rem; }
    </style>
</head>
<body>

    @include('invoices.partials.invoice_template', ['invoice' => $invoice])

</body>
</html>