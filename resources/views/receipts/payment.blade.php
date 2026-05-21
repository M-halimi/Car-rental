<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt #{{ $payment->id }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #d97706; padding-bottom: 20px; }
        .header h1 { color: #d97706; margin: 0; font-size: 24px; }
        .header p { margin: 5px 0 0; color: #666; }
        .details { margin-bottom: 20px; }
        .details table { width: 100%; border-collapse: collapse; }
        .details td { padding: 6px 10px; }
        .details td:first-child { font-weight: bold; color: #555; width: 140px; }
        .total-row td { border-top: 2px solid #d97706; font-weight: bold; font-size: 14px; padding-top: 10px; }
        .status { display: inline-block; padding: 4px 12px; border-radius: 4px; font-size: 11px; font-weight: bold; }
        .status.completed { background: #d1fae5; color: #065f46; }
        .footer { text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; color: #999; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>CarRental.ma</h1>
        <p>Payment Receipt</p>
    </div>

    <div class="details">
        <table>
            <tr><td>Receipt #</td><td>{{ $payment->id }}</td></tr>
            <tr><td>Booking #</td><td>{{ $booking->id }}</td></tr>
            <tr><td>Customer</td><td>{{ $customer?->first_name }} {{ $customer?->last_name }}</td></tr>
            <tr><td>Amount</td><td>{{ number_format($payment->amount, 2) }} MAD</td></tr>
            <tr><td>Deposit</td><td>{{ number_format($payment->deposit_amount ?? 0, 2) }} MAD</td></tr>
            <tr><td>Payment Method</td><td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td></tr>
            <tr><td>Status</td><td><span class="status {{ $payment->status }}">{{ ucfirst($payment->status) }}</span></td></tr>
            <tr><td>Transaction ID</td><td>{{ $payment->transaction_id ?? '-' }}</td></tr>
            <tr><td>Paid At</td><td>{{ $payment->paid_at?->format('M d, Y H:i') ?? '-' }}</td></tr>
            <tr class="total-row"><td>Total Paid</td><td>{{ number_format($payment->amount, 2) }} MAD</td></tr>
        </table>
    </div>

    <div class="footer">
        <p>CarRental.ma - &copy; {{ date('Y') }}. All rights reserved.</p>
        <p>Thank you for choosing CarRental.ma!</p>
    </div>
</body>
</html>
