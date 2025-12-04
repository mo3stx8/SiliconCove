<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiliconCove - {{ $order->order_no }}</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            font-style: normal;
            font-weight: normal;
            src: url("https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/fonts/DejaVuSans.ttf") format("truetype");
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            /* Use DejaVu Sans for better UTF-8 support */
            margin: 0;
            padding: 0;
        }

        .invoice-container {
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
        }

        .details,
        .items {
            width: 100%;
            margin-bottom: 20px;
        }

        .details th,
        .details td,
        .items th,
        .items td {
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
        }

        .items th {
            background-color: #f4f4f4;
        }

        .total {
            text-align: right;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <div class="header">
            <h1>SiliconCove</h1>
            <p>Order #: {{ $order->order_no }}</p>
        </div>

        <table class="details">
            <tr>
                <th>Customer Name:</th>
                <td>{{ $order->user->name }}</td>
            </tr>
            <tr>
                <th>Order Date:</th>
                <td>{{ $order->created_at->format('Y-m-d') }}</td>
            </tr>
            <tr>
                <th>Payment Method:</th>
                <td>{{ strtoupper($order->payment_method) }}</td>
            </tr>
        </table>

        <table class="items">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $order->product->name }}</td>
                    <td>{{ $order->product->description }}</td>
                    <td>{{ $order->quantity }}</td>
                    <td>${{ number_format($order->product->price, 2) }}</td>
                    <td>${{ number_format($order->total_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <p class="total">Grand Total: ${{ number_format($order->total_amount, 2) }}</p>
    </div>
</body>

</html>
