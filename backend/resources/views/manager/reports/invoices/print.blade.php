<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            color: #111827;
        }

        h1, h2, h3, p {
            margin: 0 0 10px 0;
        }

        .header {
            margin-bottom: 30px;
        }

        .section {
            margin-bottom: 30px;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #d1d5db;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #f3f4f6;
        }

        .totals {
            margin-top: 20px;
            width: 320px;
            margin-left: auto;
        }

        .totals p,
        .totals h3 {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        @media print {
            body {
                margin: 20px;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h1>Invoice #{{ $invoice->id }}</h1>
        <p>Status: {{ ucfirst($invoice->status) }}</p>
    </div>

    <div class="section grid">
        <div>
            <h3>Invoice Details</h3>
            <p><strong>Child:</strong> {{ $invoice->child->first_name ?? '' }} {{ $invoice->child->last_name ?? '' }}</p>
            <p><strong>Parent:</strong> {{ $invoice->parent->name ?? 'N/A' }}</p>
        </div>

        <div>
            <h3>Billing Period</h3>
            <p><strong>Period:</strong> {{ $invoice->period_start }} to {{ $invoice->period_end }}</p>
            <p><strong>Due Date:</strong> {{ $invoice->due_date }}</p>
        </div>
    </div>

    <div class="section">
        <h3>Line Items</h3>

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoice->items as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->qty }}</td>
                        <td>€{{ number_format($item->unit_price, 2) }}</td>
                        <td>€{{ number_format($item->total, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No line items added yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="totals">
        <p><span>Subtotal:</span> <span>€{{ number_format($subtotal, 2) }}</span></p>
        <p><span>Discount:</span> <span>€{{ number_format($invoice->discount, 2) }}</span></p>
        <h3><span>Final Total:</span> <span>€{{ number_format($finalTotal, 2) }}</span></h3>
    </div>

</body>
</html>