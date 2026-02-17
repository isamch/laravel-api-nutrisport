<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2c3e50;">Order Confirmation #{{ $order->id }}</h2>
        
        <p>Hello {{ $order->user->name }},</p>
        <p>Thank you for your order! Here are the details:</p>
        
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <thead>
                <tr style="background: #f4f4f4;">
                    <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Product</th>
                    <th style="padding: 10px; text-align: center; border: 1px solid #ddd;">Quantity</th>
                    <th style="padding: 10px; text-align: right; border: 1px solid #ddd;">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd;">{{ $item->product->name }}</td>
                    <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">{{ $item->quantity }}</td>
                    <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">€{{ number_format($item->price_at_purchase, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background: #f4f4f4; font-weight: bold;">
                    <td colspan="2" style="padding: 10px; border: 1px solid #ddd;">Total</td>
                    <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">€{{ number_format($order->total, 2) }}</td>
                </tr>
            </tfoot>
        </table>
        
        <p><strong>Delivery Address:</strong><br>
        {{ $order->address->street }}<br>
        {{ $order->address->postal_code }} {{ $order->address->city }}<br>
        {{ $order->address->country }}</p>
        
        <p><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
        <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
        
        <p style="margin-top: 30px;">Best regards,<br>NutriSport Team</p>
    </div>
</body>
</html>
