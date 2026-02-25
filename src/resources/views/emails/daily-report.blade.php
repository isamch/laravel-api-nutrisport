<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        h2 { color: #333; }
    </style>
</head>
<body>
    <h1>Rapport Quotidien - {{ $data['date'] }}</h1>

    <h2>Produits les plus/moins vendus</h2>
    <table>
        <tr>
            <th>Type</th>
            <th>Produit</th>
            <th>Quantité</th>
        </tr>
        @if($data['most_sold'])
        <tr>
            <td>Plus vendu</td>
            <td>{{ $data['most_sold']->name }}</td>
            <td>{{ $data['most_sold']->total_qty }}</td>
        </tr>
        @endif
        @if($data['least_sold'])
        <tr>
            <td>Moins vendu</td>
            <td>{{ $data['least_sold']->name }}</td>
            <td>{{ $data['least_sold']->total_qty }}</td>
        </tr>
        @endif
    </table>

    <h2>Chiffre d'affaires par produit</h2>
    <table>
        <tr>
            <th>Type</th>
            <th>Produit</th>
            <th>CA (€)</th>
        </tr>
        @if($data['max_revenue'])
        <tr>
            <td>CA Max</td>
            <td>{{ $data['max_revenue']->name }}</td>
            <td>{{ number_format($data['max_revenue']->revenue, 2) }}</td>
        </tr>
        @endif
        @if($data['min_revenue'])
        <tr>
            <td>CA Min</td>
            <td>{{ $data['min_revenue']->name }}</td>
            <td>{{ number_format($data['min_revenue']->revenue, 2) }}</td>
        </tr>
        @endif
    </table>

    <h2>Chiffre d'affaires par site</h2>
    <table>
        <tr>
            <th>Site</th>
            <th>CA (€)</th>
        </tr>
        @foreach($data['site_revenue'] as $site)
        <tr>
            <td>{{ $site->name }}</td>
            <td>{{ number_format($site->revenue, 2) }}</td>
        </tr>
        @endforeach
    </table>
</body>
</html>
