<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket_{{ $venta->folio }}</title>
    <style>
        /* Optimización para papel de 58mm */
        @page { margin: 0; }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.2;
            width: 58mm;
            margin: 0;
            padding: 0;
            background-color: white;
            color: black;
        }
        .ticket {
            width: 48mm; /* Área de impresión real segura */
            padding: 2mm;
            margin: 0 auto;
        }
        .centered { text-align: center; }
        .bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        
        .header { margin-bottom: 10px; }
        .header h1 { font-size: 16px; margin: 0; }
        
        .separator { 
            border-top: 1px dashed black; 
            margin: 5px 0; 
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
        }
        th { 
            text-align: left; 
            border-bottom: 1px solid black;
            font-size: 10px;
        }
        td { padding: 2px 0; vertical-align: top; }
        
        .total-section {
            text-align: right;
            margin-top: 5px;
        }
        .total-row { font-size: 14px; font-weight: bold; }
        
        .footer {
            margin-top: 15px;
            font-size: 10px;
        }

        .metodo-pago-box {
            border: 1px solid black;
            padding: 3px;
            text-align: center;
            margin-top: 5px;
            font-size: 10px;
        }

        @media print {
            .btn-imprimir { display: none; }
        }
    </style>
</head>
<body onload="window.print();">

    <div class="ticket">
        <div class="header centered">
            <h1 class="uppercase">ABARROTES</h1>
            <p class="uppercase" style="font-size: 9px;">Punto de Venta</p>
        </div>

        <div class="separator"></div>

        <div>
            <p><strong>FOLIO:</strong> {{ $venta->folio }}</p>
            <p><strong>FECHA:</strong> {{ date('d/m/Y H:i', strtotime($venta->fecha)) }}</p>
            <p><strong>CAJERO:</strong> {{ $venta->usuario->username ?? 'S/N' }}</p>
        </div>

        <div class="separator"></div>

        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">CANT</th>
                    <th style="width: 55%;">DESC</th>
                    <th style="width: 30%; text-align: right;">SUB</th>
                </tr>
            </thead>
            <tbody>
                @foreach($venta->detalles as $detalle)
                <tr>
                    <td>{{ number_format($detalle->cantidad, 0) }}</td>
                    <td class="uppercase">
                        {{ substr($detalle->producto->descripcion ?? $detalle->descripcion, 0, 15) }}
                    </td>
                    <td style="text-align: right;">${{ number_format($detalle->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="separator"></div>

        <div class="total-section">
            <p class="total-row">TOTAL: ${{ number_format($venta->total, 2) }}</p>
            
            {{-- LÓGICA SEGÚN TIPO DE PAGO --}}
            @if($venta->tipo_pago === 'efectivo')
                <p>RECIBIDO: ${{ number_format($venta->pago_cliente, 2) }}</p>
                <p>CAMBIO: ${{ number_format($venta->cambio, 2) }}</p>
            @else
                <div class="metodo-pago-box">
                    <span class="bold">PAGO: {{ strtoupper($venta->tipo_pago) }}</span>
                    @if($venta->referencia_pago)
                        <br>REF: {{ $venta->referencia_pago }}
                    @endif
                </div>
            @endif
        </div>

        <div class="footer centered">
            <p class="bold">¡GRACIAS POR SU COMPRA!</p>
            <p>Conserve su ticket</p>
            <br>
            <p>.</p> 
        </div>
    </div>

    <div class="centered">
        <button class="btn-imprimir" onclick="window.print()" style="margin-top: 20px; padding: 10px;">
            Reimprimir Ticket
        </button>
    </div>

</body>
</html>