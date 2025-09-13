<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.css') }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            max-width: 400px;
            margin: auto;
            border: 1px solid #ccc;
            padding: 20px;
            position: relative;
            top: 50px;
        }
        .invoice {
            padding: 20px;
            border: 1px solid #000;
            background-color: #f9f9f9;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2, h4 { text-align: center; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        td, th { padding: 5px; border-bottom: 1px solid #ddd; text-align: left; }
        .right { text-align: right; }
        .total { font-weight: bold; }
        .footer { margin-top: 15px; font-size: 12px; }
        .footer-note { font-style: italic; margin-top: 5px; }
        .actions { margin-top: 20px; text-align: center; }
        .action-button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 8px 16px;
            margin: 5px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .action-button i { font-size: 16px; }
        .action-button:hover { opacity: 0.9; }

        /* Masquer la section actions lors de l'impression */
        @media print {
            .actions {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="invoice" id="invoice">
        <h2>SAGER</h2>
        <p style="text-align:center;"><i class="fas fa-phone"></i> +229 0196466625</p>
        IFU: 0202586942320
        <hr>
        <h4>FACTURE PROFORMA / N° {{ $sale->id }}/{{ now()->format('m') }}/{{ now()->format('y') }}/FR-N</h4>
        <p>
            <strong>Agence:</strong> AKPAKPA | BOUTIQUE SAGER<br>
            <strong>Client:</strong> {{ $sale->buyer_name }}<br>
            <strong>Date:</strong> {{ $sale->created_at->format('d M Y - H:i:s') }}<br>
            <strong>Mode:</strong> CASH / A-AUTRE
        </p>

        <table>
            <thead>
                <tr>
                    <th>Qté</th>
                    <th>Désignation</th>
                    <th>P.U.V</th>
                    <th>Montant</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->products as $item)
                    <tr>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->product->name ?? 'Produit supprimé' }}</td>
                        <td>{{ number_format($item->price, 0, ',', ' ') }}</td>
                        <td>{{ number_format($item->price * $item->quantity, 0, ',', ' ') }} F CFA</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p><strong>Situation des emballages</strong><br>DETAILS: UNITÉ 0 / RST: 0</p>

        <table>
            <tr><td>Total HT:</td><td class="right">{{ number_format($sale->total, 0, ',', ' ') }}</td></tr>
            <tr><td>TVA 0%:</td><td class="right">0</td></tr>
            <tr><td>AIB 0%:</td><td class="right">0</td></tr>
            <tr class="total"><td>Total TTC:</td><td class="right">{{ number_format($sale->total, 0, ',', ' ') }}</td></tr>
        </table>

        <p><strong>Vendeur:</strong> {{ $sale->seller_name }}</p>

        <div class="footer">
            <p><strong>Observation :</strong> Facture à régler au plus tard le {{ $sale->created_at->addDays(2)->format('d/m/Y') }}</p>
            <p class="footer-note">Les produits vendus ne sont ni repris, ni échangés.</p>
            <p style="text-align:center;">Merci pour votre fidélité</p>
            <p style="text-align:center; font-size: 12px;">{{ $sale->seller_name }} — {{ $sale->created_at->format('d/m/Y') }} — {{ $sale->created_at->format('H:i:s') }}</p>
        </div>

        <div class="actions">
            <button class="action-button" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimer
            </button>
             <a href="javascript:history.back()" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>
</body>
</html>
