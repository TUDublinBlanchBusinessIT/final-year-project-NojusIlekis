<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('manager.invoice') }} #{{ $invoice->id }}</title>
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
        <h1>{{ __('manager.invoice') }} #{{ $invoice->id }}</h1>
        <p>{{ __('manager.status') }}: {{ ucfirst($invoice->status) }}</p>
    </div>

    <div class="section grid">
        <div>
            <h3>{{ __('manager.invoice_details') }}</h3>
            <p><strong>{{ __('manager.child') }}:</strong> {{ $invoice->child->first_name ?? '' }} {{ $invoice->child->last_name ?? '' }}</p>
            <p><strong>{{ __('manager.parent') }}:</strong> {{ $invoice->parent->name ?? __('manager.not_available') }}</p>
        </div>

        <div>
            <h3>{{ __('manager.billing_period') }}</h3>
            <p><strong>{{ __('manager.period') }}:</strong> {{ $invoice->period_start }} {{ __('manager.to') }} {{ $invoice->period_end }}</p>
            <p><strong>{{ __('manager.due_date') }}:</strong> {{ $invoice->due_date }}</p>
        </div>
    </div>

    <div class="section">
        <h3>{{ __('manager.line_items') }}</h3>

        <table>
            <thead>
                <tr>
                    <th>{{ __('manager.description') }}</th>
                    <th>{{ __('manager.qty') }}</th>
                    <th>{{ __('manager.unit_price') }}</th>
                    <th>{{ __('manager.total') }}</th>
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
                        <td colspan="4">{{ __('manager.no_line_items') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="totals">
        <p><span>{{ __('manager.subtotal') }}:</span> <span>€{{ number_format($subtotal, 2) }}</span></p>
        <p><span>{{ __('manager.discount') }}:</span> <span>€{{ number_format($invoice->discount, 2) }}</span></p>
        <h3><span>{{ __('manager.final_total') }}:</span> <span>€{{ number_format($finalTotal, 2) }}</span></h3>
    </div>

</body>
</html>