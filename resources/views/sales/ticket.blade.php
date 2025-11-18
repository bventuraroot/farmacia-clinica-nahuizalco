<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <title>Ticket de Venta #{{ $sale->id }}</title>
    <!-- SweetAlert2 para notificaciones -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Google Fonts para mejor tipografía -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @media print {
            @page {
                size: 80mm auto;
                width: 80mm;
                height: auto;
                margin: 0 !important;
                margin-top: 0 !important;
                margin-bottom: 0 !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                padding: 0 !important;
                /* Forzar una sola página continua - sin límites de altura */
                page-break-inside: avoid !important;
                page-break-after: avoid !important;
                page-break-before: avoid !important;
                orphans: 0 !important;
                widows: 0 !important;
            }

            /* Configuración específica para impresoras térmicas */
            @page :first {
                size: 80mm auto;
                margin: 0 !important;
            }

            @page :left, @page :right {
                size: 80mm auto;
                margin: 0 !important;
            }

            /* Forzar que todo el documento sea una sola página */
            html, body {
                height: auto !important;
                max-height: none !important;
                overflow: visible !important;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
                /* Evitar cortes automáticos en elementos */
                page-break-inside: avoid;
                break-inside: avoid;
            }

            body {
                margin: 0 !important;
                padding: 0 !important;
                font-size: 13px !important;
                background: white !important;
                -webkit-print-color-adjust: exact;
                /* Forzar una sola página */
                page-break-inside: avoid !important;
                page-break-after: avoid !important;
                page-break-before: avoid !important;
                height: auto !important;
                max-height: none !important;
            }

            .no-print {
                display: none !important;
            }

            /* Mantener secciones juntas y forzar una sola página */
            .ticket-container {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                page-break-after: avoid !important;
                page-break-before: avoid !important;
                height: auto !important;
                max-height: none !important;
                display: block !important;
            }

            .header, .issuer-data, .invoice-data, .receiver-data, .products, .totals, .payments, .footer {
                page-break-inside: avoid;
                break-inside: avoid;
            }

            /* Evitar cortes en tablas */
            .product-table {
                page-break-inside: avoid;
                break-inside: avoid;
            }

            .product-table tr {
                page-break-inside: avoid;
                break-inside: avoid;
            }

            /* Ocultar cualquier control del navegador */
            @page {
                size: 80mm auto;
                margin: 0;
                page-break-inside: avoid;
            }

            /* Forzar que todo el contenido se mantenga en una sola página */
            .ticket-container {
                height: auto !important;
                max-height: none !important;
                min-height: auto !important;
                overflow: visible !important;
                display: block !important;
                page-break-inside: avoid !important;
                page-break-after: avoid !important;
                page-break-before: avoid !important;
            }

            /* Comando de corte automático para impresoras térmicas */
            body::after {
                content: "";
                display: block;
                height: 0;
                /* Comando ESC/POS para corte de papel */
                /* \x1D\x56\x00 = GS V 0 (corte parcial) */
                /* \x1D\x56\x01 = GS V 1 (corte total) */
                font-family: monospace;
                white-space: pre;
                line-height: 0;
                font-size: 0;
            }
        }

        body {
            font-family: 'Inter', 'Arial', 'Helvetica', sans-serif;
            width: 80mm;
            margin: 0 auto;
            padding: 2mm;
            font-size: 12px;
            line-height: 1.3;
            background: white;
            font-weight: 400;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            /* Forzar una sola página continua */
            page-break-inside: avoid;
            page-break-after: avoid;
            page-break-before: avoid;
            /* Asegurar que el texto no se salga del margen */
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .ticket-container {
            width: 100%;
            /* Forzar una sola página continua */
            page-break-inside: avoid;
            page-break-after: avoid;
            page-break-before: avoid;
            display: block;
            /* Indicar al navegador que este es un contenedor de impresión continua */
            contain: layout style;
            /* Permitir que el contenido determine la altura */
            height: auto;
            max-height: none;
            min-height: auto;
        }

        /* Header con logo y datos de la empresa */
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 2px;
            margin-bottom: 2px;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 3px;
        }

        .logo {
            max-width: 110px;
            max-height: 110px;
            height: auto;
            width: auto;
            filter: grayscale(100%);
        }

        .company-name {
            font-weight: 700;
            font-size: 13px;
            margin-bottom: 2px;
            text-transform: uppercase;
            word-wrap: break-word;
            letter-spacing: 0.5px;
        }

        .company-info {
            font-size: 12px;
            line-height: 1.3;
            word-wrap: break-word;
        }

        /* Datos del emisor */
        .issuer-data {
            margin-bottom: 3px;
            font-size: 11px;
            border-bottom: 1px dashed #000;
            padding-bottom: 2px;
            word-wrap: break-word;
        }

        .issuer-title {
            font-weight: 600;
            text-align: center;
            margin-bottom: 2px;
            font-size: 11px;
            letter-spacing: 0.3px;
        }

        .issuer-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .issuer-label {
            font-weight: 600;
        }

        /* Datos de la factura */
        .invoice-data {
            margin-bottom: 3px;
            font-size: 11px;
            border-bottom: 1px dashed #000;
            padding-bottom: 2px;
            word-wrap: break-word;
        }

        .invoice-title {
            font-weight: 600;
            text-align: center;
            margin-bottom: 2px;
            font-size: 11px;
            letter-spacing: 0.3px;
        }

        .invoice-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .invoice-label {
            font-weight: 600;
        }

        /* Datos del receptor */
        .receiver-data {
            margin-bottom: 3px;
            font-size: 11px;
            border-bottom: 1px dashed #000;
            padding-bottom: 2px;
            word-wrap: break-word;
        }

        .receiver-title {
            font-weight: 600;
            text-align: center;
            margin-bottom: 2px;
            font-size: 11px;
            letter-spacing: 0.3px;
        }

        .receiver-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .receiver-label {
            font-weight: 600;
        }

        .email-truncated {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: inline-block;
        }

        /* Estilos para texto largo que se ajuste al ancho */
        .long-text {
            font-size: 7px;
            word-break: break-all;
            line-height: 1.1;
            max-width: 100%;
            overflow-wrap: break-word;
        }

        /* Estilos para códigos DTE */
        .dte-code {
            font-size: 8px;
            word-break: break-all;
            line-height: 1.2;
            font-family: 'Courier New', monospace;
        }

        /* Estilos para códigos DTE en líneas separadas */
        .invoice-row-dte {
            margin-bottom: 4px;
        }

        .invoice-label-dte {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 2px;
        }

        .dte-code-large {
            font-size: 10px;
            word-break: break-all;
            line-height: 1.2;
            font-family: 'Courier New', 'Consolas', monospace;
            margin-bottom: 3px;
            font-weight: 600;
        }

        /* Productos */
        .products {
            margin: 3px 0;
            font-size: 11px;
            word-wrap: break-word;
        }

        .product-header {
            font-weight: 600;
            text-align: center;
            margin-bottom: 3px;
            font-size: 11px;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
            letter-spacing: 0.3px;
        }

        .product-table {
            width: 100%;
            border-collapse: collapse;
        }

        .product-table th {
            font-weight: 600;
            font-size: 10px;
            text-align: center;
            border-bottom: 1px solid #000;
            padding: 2px 0;
            letter-spacing: 0.2px;
        }

        .product-table td {
            font-size: 10px;
            padding: 2px 0;
            word-wrap: break-word;
            /*border-bottom: 1px dotted #ccc;*/
        }

        .product-name {
            font-weight: 600;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Totales */
        .totals {
            margin-top: 3px;
            font-size: 11px;
            border-top: 1px dashed #000;
            padding-top: 2px;
            word-wrap: break-word;
        }

        .total-title {
            font-weight: 600;
            text-align: center;
            margin-bottom: 2px;
            font-size: 11px;
            letter-spacing: 0.3px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .total-final {
            font-weight: 700;
            font-size: 11px;
            border-top: 1px solid #000;
            padding-top: 3px;
            margin-top: 3px;
            letter-spacing: 0.3px;
        }

        /* Pagos */
        .payments {
            margin-top: 3px;
            font-size: 12px;
            border-top: 1px dashed #000;
            padding-top: 2px;
        }

        .payment-title {
            font-weight: bold;
            text-align: center;
            margin-bottom: 2px;
            font-size: 13px;
        }

        .payment-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .payment-label {
            font-weight: bold;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 3px;
            font-size: 10px;
            border-top: 1px dashed #000;
            padding-top: 3px;
            word-wrap: break-word;
        }

        .footer-message {
            font-weight: bold;
            margin-bottom: 3px;
        }

        .footer-info {
            font-size: 10px;
            line-height: 1.2;
        }

        /* Botones y controles */
        .print-button {
            margin: 20px auto;
            display: block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .print-button:hover {
            background-color: #0056b3;
        }

        .d-flex {
            display: flex;
        }

        .align-items-center {
            align-items: center;
        }

        .gap-2 {
            gap: 0.5rem;
        }

        .badge {
            display: inline-block;
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 700;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }

        .bg-warning {
            background-color: #ffc107;
            color: #000;
        }

        .bg-success {
            background-color: #28a745;
        }

        .mb-3 {
            margin-bottom: 1rem;
        }

        /* QR Code para impresoras térmicas */
        .qr-container {
            text-align: center;
            margin: 8px 0;
            padding: 4px;
            border: 1px dashed #000;
        }

        .qr-container svg {
            width: 60px !important;
            height: 60px !important;
            border: 1px solid #000;
            background: white;
        }

        .qr-placeholder {
            width: 60px;
            height: 60px;
            border: 1px solid #000;
            display: inline-block;
            line-height: 60px;
            font-size: 8px;
            color: #000;
            background: white;
        }

        /* Sección DTE para impresoras térmicas */
        .dte-section {
            margin: 8px 0;
            font-size: 12px;
            border: 1px solid #000;
            padding: 6px;
        }

        .dte-title {
            font-weight: bold;
            text-align: center;
            margin-bottom: 4px;
            font-size: 13px;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
        }

        .dte-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-size: 11px;
        }

        .dte-label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Botón de impresión (no se imprime) -->
    <!--<div class="no-print">
        <!-- Contenedor para selector de impresoras -->
        <!--<div id="printer-selector-container" class="mb-3">
            <!-- Fallback si el JavaScript no carga -->
            <!--<div id="printer-fallback" style="display: block;">
                <label style="font-weight: bold; margin-bottom: 5px; display: block;">
                    🖨️ Impresora:
                </label>
                <select style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 8px;">
                    <option>Impresora Predeterminada del Sistema</option>
                    <option>Epson TM-T88V (Recomendada)</option>
                    <option>Star TSP650 (Recomendada)</option>
                    <option>Bixolon SRP-350 (Recomendada)</option>
                    <option>Citizen CT-S310A (Recomendada)</option>
                    <option>POS-80 Series (Recomendada)</option>
                </select>
                <small style="color: #666; font-size: 12px;">
                    Configure su impresora de 80mm como predeterminada en el sistema
                </small>-->

                <!-- Información sobre cortes automáticos -->
                <!--<div style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; padding: 8px; margin-top: 8px;">
                    <strong style="color: #856404;">⚠️ Importante:</strong>
                    <div style="color: #856404; font-size: 11px; margin-top: 4px;">
                        Si la impresora corta automáticamente en 3 partes, configure en la impresora:<br>
                        • <strong>Auto Cut:</strong> OFF<br>
                        • <strong>Paper Cut:</strong> Manual<br>
                        • <strong>Cut Mode:</strong> Full Cut Only
                    </div>
                </div>
            </div>
        </div>-->

        <!-- Información de impresora seleccionada -->
        <!--<div id="printer-info-display"></div>-->

        <!--<div class="gap-2 d-flex align-items-center">
            <button class="print-button" onclick="window.print()">
                🖨️ Imprimir Ticket
            </button>
            @if(!$autoprint)
                <span class="badge bg-warning">Modo Vista</span>
            @else
                <span class="badge bg-success">Auto-Impresión</span>
            @endif
        </div>
    </div>-->

    <div class="ticket-container">
        <!-- Encabezado con logo -->
        <div class="header">
            <div class="logo-container">
                <img src="{{ asset('assets/img/logo/logogrises.png') }}" alt="Logo" class="logo">
            </div>
            <div class="company-name">{{ $sale->company->name ?? 'AGROSERVICIO MILAGRO DE DIOS' }}</div>
        </div>

        <!-- Datos del emisor -->
        <div class="issuer-data">
            <div class="issuer-title">DATOS DEL EMISOR</div>
            <div class="issuer-row">
                <span class="issuer-label">Nombre:</span>
                <span>RUDY ELENILSON CHINCHILLA AGUILAR</span>
            </div>
            @if($sale->company->nit)
                <div class="issuer-row">
                    <span class="issuer-label">NIT:</span>
                    <span>{{ $sale->company->nit }}</span>
                </div>
            @endif
            @if($sale->company->nrc)
                <div class="issuer-row">
                    <span class="issuer-label">NRC:</span>
                    <span>{{ $sale->company->nrc }}</span>
                </div>
            @endif
            @if($sale->company->address)
                <div class="issuer-row">
                    <span class="issuer-label">Dirección:</span>
                    <span style="font-size: 11px;">
                        @if(is_string($sale->company->address))
                            {{ $sale->company->address }}
                        @elseif(is_object($sale->company->address) && isset($sale->company->address->reference))
                            {{ $sale->company->address->reference }}
                        @else
                            {{ $sale->company->address }}
                        @endif
                    </span>
                </div>
            @endif
            @if($sale->company->phone)
                <div class="issuer-row">
                    <span class="issuer-label">Tel:</span>
                    <span>
                        @if(is_string($sale->company->phone))
                            {{ $sale->company->phone }}
                        @elseif(is_object($sale->company->phone) && isset($sale->company->phone->phone))
                            {{ $sale->company->phone->phone }}
                        @else
                            {{ $sale->company->phone }}
                        @endif
                    </span>
                </div>
            @endif
            <!-- Información adicional de la empresa -->
            @if(isset($sale->dte) && $sale->dte && $sale->dte->json)
                @php
                    $dteJson = is_string($sale->dte->json) ? json_decode($sale->dte->json, true) : $sale->dte->json;
                @endphp
                @if(isset($dteJson['emisor'][0]['descActividad']) && $dteJson['emisor'][0]['descActividad'])
                    <div class="issuer-row">
                        <span class="issuer-label">Actividad Económica:</span>
                        <span style="font-size: 12px;">{{ $dteJson['emisor'][0]['descActividad'] }}</span>
                    </div>
                @endif
            @endif
        </div>

        <!-- Datos del documento -->
        <div class="invoice-data">
            <div class="invoice-title">DATOS DEL DOCUMENTO</div>

            <!-- Tipo de documento -->
            <div class="invoice-row">
                <span class="invoice-label">Tipo:</span>
                <span style="font-size: 9px;">
                    @if($sale->typedocument)
                        {{ $sale->typedocument->description ?? 'FACTURA' }}
                        @if($sale->typedocument->type)
                            ({{ $sale->typedocument->type }})
                        @endif
                    @else
                        COMPROBANTE DE CREDITO FISCAL
                    @endif
                </span>
            </div>


            <!-- Información DTE optimizada -->
            @if(isset($sale->dte) && $sale->dte)
                @if($sale->dte->selloRecibido)
                    <div class="invoice-row-dte">
                        <div class="invoice-label-dte">Sello de Recepción:</div>
                        <div class="dte-code-large">{{ $sale->dte->selloRecibido }}</div>
                    </div>
                @endif
                @if($sale->dte->codigoGeneracion)
                    <div class="invoice-row-dte">
                        <div class="invoice-label-dte">Código de Generación:</div>
                        <div class="dte-code-large">{{ $sale->dte->codigoGeneracion }}</div>
                    </div>
                @endif
                @if($sale->dte->id_doc)
                    <div class="invoice-row-dte">
                        <div class="invoice-label-dte">Número Control del DTE:</div>
                        <div class="dte-code-large">{{ $sale->dte->id_doc }}</div>
                    </div>
                @endif
            @endif

            <!-- Sucursal -->
            <!--<div class="invoice-row">
                <span class="invoice-label">Sucursal:</span>
                <span>{{ $sale->company->name ?? 'Casa Matriz' }}</span>
            </div>-->

            <!-- Fecha -->
            <!--<div class="invoice-row">
                <span class="invoice-label">Fecha:</span>
                <span>{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</span>
            </div>-->

            <!-- Fecha y hora de recepción del DTE -->
            @if(isset($sale->dte) && $sale->dte && $sale->dte->fhRecibido)
                <div class="invoice-row">
                    <span class="invoice-label">Fecha Recepción DTE:</span>
                    <span style="font-size: 10px;">{{ \Carbon\Carbon::parse($sale->dte->fhRecibido)->format('d/m/Y H:i:s') }}</span>
                </div>
            @endif

            <!-- Número de documento -->
            <!--<div class="invoice-row">
                <span class="invoice-label">Número:</span>
                <span>
                    @if($sale->nu_doc)
                        {{ $sale->nu_doc }}
                    @else
                        {{ $sale->id }}
                    @endif
                </span>
            </div>-->

            <!-- Caja -->
            <!-- <div class="invoice-row">
                <span class="invoice-label">Caja:</span>
                <span>001</span>
            </div>-->
        </div>

        <!-- Datos del receptor -->
        <div class="receiver-data">
            <div class="receiver-title">DATOS DE RECEPTOR</div>
            <div class="receiver-row">
                <span class="receiver-label">Nombre:</span>
                <span>
                    @if($sale->client)
                        @if($sale->client->tpersona == 'N')
                            {{ $sale->client->firstname }} {{ $sale->client->secondname ?? '' }} {{ $sale->client->firstlastname }} {{ $sale->client->secondlastname ?? '' }}
                        @else
                            {{ $sale->client->name_contribuyente ?? $sale->client->comercial_name ?? 'Cliente Empresa' }}
                        @endif
                    @else
                        CONSUMIDOR FINAL
                    @endif
                </span>
            </div>

            @if($sale->client)
                @if($sale->client->nit)
                    <div class="receiver-row">
                        <span class="receiver-label">Documento:</span>
                        <span>{{ $sale->client->nit }}</span>
                    </div>
                @endif

                @if($sale->client->ncr)
                    <div class="receiver-row">
                        <span class="receiver-label">NCR:</span>
                        <span>{{ $sale->client->ncr }}</span>
                    </div>
                @endif

                @if($sale->client->email)
                    <div class="receiver-row">
                        <span class="receiver-label">Correo:</span>
                        <span class="email-truncated" title="{{ $sale->client->email }}">
                            {{ strlen($sale->client->email) > 25 ? substr($sale->client->email, 0, 22) . '...' : $sale->client->email }}
                        </span>
                    </div>
                @endif

                @if($sale->client->phone)
                    <div class="receiver-row">
                        <span class="receiver-label">Tel:</span>
                        <span>
                            @if(is_string($sale->client->phone))
                                {{ $sale->client->phone }}
                            @elseif(is_object($sale->client->phone))
                                @if($sale->client->phone->phone)
                                    {{ $sale->client->phone->phone }}
                                @endif
                                @if($sale->client->phone->phone_fijo)
                                    @if($sale->client->phone->phone) | @endif
                                    {{ $sale->client->phone->phone_fijo }}
                                @endif
                            @else
                                {{ $sale->client->phone }}
                            @endif
                        </span>
                    </div>
                @endif
            @endif
        </div>

        <!-- Sección DTE (para facturas y créditos fiscales) -->
        <!--@if(isset($isFacturaOrCredito) && $isFacturaOrCredito)
        <div class="dte-section">
            <div class="dte-title">DOCUMENTO TRIBUTARIO ELECTRÓNICO</div>
            @if($hasDte && $sale->dte)
                @if($sale->dte->codigoGeneracion)
                    <div class="dte-row">
                        <span class="dte-label">Código Generación:</span>
                        <span>{{ $sale->dte->codigoGeneracion }}</span>
                    </div>
                @endif
                @if($sale->dte->id_doc)
                    <div class="dte-row">
                        <span class="dte-label">Número Control:</span>
                        <span>{{ $sale->dte->id_doc }}</span>
                    </div>
                @endif
                @if($sale->dte->Estado)
                    <div class="dte-row">
                        <span class="dte-label">Estado:</span>
                        <span>{{ $sale->dte->Estado }}</span>
                    </div>
                @endif
                @if($sale->dte->fhRecibido)
                    <div class="dte-row">
                        <span class="dte-label">Fecha Recepción:</span>
                        <span>{{ \Carbon\Carbon::parse($sale->dte->fhRecibido)->format('d/m/Y H:i:s') }}</span>
                    </div>
                @endif
                @if($sale->dte->selloRecibido)
                    <div class="dte-row">
                        <span class="dte-label">Sello Recepción:</span>
                        <span>{{ substr($sale->dte->selloRecibido, 0, 30) }}...</span>
                    </div>
                @endif
            @else
                <div class="dte-row">
                    <span class="dte-label">Estado:</span>
                    <span>PENDIENTE DE PROCESAMIENTO</span>
                </div>
                <div class="dte-row">
                    <span class="dte-label">Información:</span>
                    <span>El documento será procesado por Hacienda</span>
                </div>
            @endif
        </div>
        @endif-->

        <!-- Productos -->
        <div class="products">
            <div class="product-header">PRODUCTOS</div>
            <table class="product-table">
                <thead>
                    <tr>
                        <th style="width: 10%;">Cant.</th>
                        <th style="width: 38%;">Descripción</th>
                        <th style="width: 26%;">Precio</th>
                        <th style="width: 26%;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->details as $detail)
                        <tr>
                            <td class="text-center">{{ $detail->amountp }}</td>
                            <td class="product-name">
                                {{ $detail->product->name ?? 'Producto' }}
                                @if($detail->unit_name)
                                    <br><small style="font-size: 9px; color: #666;">
                                        {{ $detail->unit_name }}
                                        @if($detail->product && $detail->product->rubro)
                                            - {{ $detail->product->rubro->name ?? '' }}
                                        @endif
                                    </small>
                                @elseif($detail->product && $detail->product->rubro)
                                    <br><small style="font-size: 9px; color: #666;">
                                        {{ $detail->product->rubro->name ?? '' }}
                                        @if($detail->product->unit)
                                            - {{ $detail->product->unit }}
                                        @endif
                                        @if($detail->product->presentation)
                                            - {{ $detail->product->presentation }}
                                        @endif
                                    </small>
                                @endif
                            </td>
                            <td class="text-right">${{ number_format($detail->priceunit, 4) }}</td>
                            <td class="text-right">${{ number_format($detail->pricesale + $detail->nosujeta + $detail->exempt, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totales -->
        <div class="totals">
            <div class="total-title">SUMATORIA TOTAL DE OPERACIONES</div>
            <!--<div class="total-row">
                <span>Sumatoria:</span>
                <span class="text-right">${{ number_format($subtotal + $totalIva, 2) }}</span>
            </div>-->
            <div class="total-row">
                <span>Subtotal:</span>
                <span class="text-right">${{ number_format($subtotal, 2) }}</span>
            </div>
            @if($totalIva > 0)
                <div class="total-row">
                    <span>IVA (13%):</span>
                    <span class="text-right">${{ number_format($totalIva, 2) }}</span>
                </div>
            @endif
            <div class="total-row total-final">
                <span>TOTAL:</span>
                <span class="text-right">${{ number_format($total, 2) }}</span>
            </div>
        </div>

        <!-- Pagos -->
       <!--<div class="payments">
            <div class="payment-title">CONDICIÓN DE PAGO</div>
            <div class="payment-row">
                <span class="payment-label">
                    Cond. Pago:
                </span>
                <span>
                    @switch($sale->waytopay)
                        @case(1) EFECTIVO @break
                        @case(2) CRÉDITO @break
                        @case(3) OTRO @break
                        @default EFECTIVO
                    @endswitch
                </span>
            </div>
            <div class="payment-row total-final">
                <span class="payment-label">EFECTIVO RECIBIDO:</span>
                <span class="text-right">${{ number_format($total, 2) }}</span>
            </div>
        </div>-->

        <!-- Código QR (para facturas y créditos fiscales) -->
        @if(isset($isFacturaOrCredito) && $isFacturaOrCredito)
        <div style="text-align: center; margin: 10px 0;">
            @if($hasDte && $sale->dte && isset($qrCode) && $qrCode)
                <div style="margin-bottom: 5px;">
                    {!! $qrCode !!}
                </div>
                <div style="font-size: 12px; font-weight: bold; margin-bottom: 2px;">
                    DOCUMENTO TRIBUTARIO ELECTRÓNICO
                </div>
                <div style="font-size: 10px;">
                    Escanea para verificar
                </div>
            @else
                <div style="margin-bottom: 5px;">
                    <div style="width: 80px; height: 80px; border: 2px solid #000; margin: 0 auto; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: bold;">
                        QR DTE
                    </div>
                </div>
                <div style="font-size: 12px; font-weight: bold; margin-bottom: 2px;">
                    DOCUMENTO TRIBUTARIO ELECTRÓNICO
                </div>
                <div style="font-size: 10px;">
                    Disponible después del procesamiento.
                </div>
            @endif
        </div>
        @endif

        <!-- Pie del ticket -->
        <div class="footer">
            <div class="footer-message">---CUENTA CERRADA---</div>
            <div class="footer-info">
                <div style="margin-bottom: 4px;">
                    <span style="font-weight: bold;">Moneda:</span> USD
                </div>
                <!--<div style="margin-bottom: 4px;">
                    <span style="font-weight: bold;">Modelo:</span> PREVIO
                </div>-->
                <div style="margin-bottom: 4px;">
                    <span style="font-weight: bold;">Tipo Trasmisión:</span> NORMAL
                </div>
                <div style="margin-bottom: 4px;">
                    <span style="font-weight: bold;">Ambiente:</span> PRODUCCIÓN
                </div>
                @if(isset($isFacturaOrCredito) && $isFacturaOrCredito)
                    @if($sale->typedocument)
                        {{ $sale->typedocument->description }} Electrónica DTE
                    @else
                        Factura Electrónica DTE
                    @endif
                @else
                    Ticket de Venta
                @endif<br>
                {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}
            </div>
            <!--@if($sale->acuenta && $sale->acuenta != 'Venta al menudeo')
                <div style="margin-top: 6px; font-size: 11px;">
                    {{ $sale->acuenta }}
                </div>
            @endif-->
        </div>
    </div>

    <!-- Comandos de corte automático para impresoras térmicas -->
    <div id="cut-commands" style="display: none; font-family: monospace; white-space: pre; line-height: 0; font-size: 0;">
        <!-- Comandos ESC/POS para corte de papel -->
        <!-- \x1D\x56\x00 = GS V 0 (corte parcial) -->
        <!-- \x1D\x56\x01 = GS V 1 (corte total) -->
        <!-- \x1B\x64\x03 = ESC d 3 (avanzar 3 líneas antes del corte) -->
    </div>

    <!-- Script para detección de impresoras (versión simplificada) -->
    <script src="{{ asset('assets/js/printer-detection-simple.js') }}"></script>

    <!-- Script de verificación -->
    <script>
        // Verificar que el archivo se cargó
        setTimeout(function() {
            if (typeof printerDetector === 'undefined' || !printerDetector) {
                console.warn('⚠️ PrinterDetector no se cargó, usando fallback');

                // Activar funcionalidad del fallback
                const fallback = document.getElementById('printer-fallback');
                if (fallback) {
                    fallback.style.display = 'block';
                    const fallbackSelect = fallback.querySelector('select');
                    if (fallbackSelect) {
                        fallbackSelect.addEventListener('change', function() {
                            console.log('🖨️ Impresora seleccionada (fallback):', this.value);
                        });
                    }
                }
            } else {
                console.log('✅ PrinterDetector cargado y funcionando');
            }
        }, 1000);
    </script>

    <script>
        let hasAutoprinted = false;

        // Función mejorada de impresión con información de impresora
        function printWithPrinterInfo() {
            const selectedPrinter = getPrinterInfo();

            if (selectedPrinter) {
                console.log('Imprimiendo con:', selectedPrinter.name);

                // Mostrar información antes de imprimir (solo si no es auto-impresión)
                if (!hasAutoprinted && typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Imprimiendo Ticket',
                        text: `Enviando a: ${selectedPrinter.name}`,
                        icon: 'info',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            }

            // Forzar una sola página antes de imprimir
            forceSinglePage();

            // Agregar comandos de corte automático antes de imprimir
            addCutCommands();

            // Configurar impresión para evitar cortes automáticos
            const printSettings = {
                silent: false,
                printBackground: true,
                paperWidth: 80, // 80mm
                paperHeight: 'auto',
                marginsType: 1, // Sin márgenes
                shouldPrintBackgrounds: true,
                shouldPrintSelectionOnly: false,
                // Configuraciones específicas para evitar cortes
                pageRanges: [],
                landscape: false,
                scaleFactor: 100
            };

            // Intentar usar la API de impresión del navegador si está disponible
            if (window.navigator && window.navigator.printing) {
                window.navigator.printing.print(printSettings);
            } else {
                // Fallback: impresión normal
                window.print();
            }
        }

        // Auto-imprimir al cargar solo si está habilitado
        const autoprint = {{ $autoprint ? 'true' : 'false' }};
        const autoClose = new URLSearchParams(window.location.search).get('auto_close') === 'true';

        // Función de impresión silenciosa
        // Función para agregar comandos de corte automático
        function addCutCommands() {
            try {
                // Crear un elemento temporal con comandos ESC/POS para corte
                const cutElement = document.createElement('div');
                cutElement.style.cssText = 'position: absolute; left: -9999px; font-family: monospace; white-space: pre; font-size: 1px; line-height: 0;';

                // Comandos ESC/POS para corte de papel
                // \x1B\x64\x03 = ESC d 3 (avanzar 3 líneas)
                // \x1D\x56\x00 = GS V 0 (corte parcial)
                cutElement.textContent = '\x1B\x64\x03\x1D\x56\x00';

                // Agregar al final del body
                document.body.appendChild(cutElement);

                console.log('🔄 Comandos de corte automático agregados');

                // Remover después de un tiempo
                setTimeout(() => {
                    if (cutElement.parentNode) {
                        cutElement.parentNode.removeChild(cutElement);
                    }
                }, 1000);

            } catch (error) {
                console.error('Error agregando comandos de corte:', error);
            }
        }

        // Función para calcular y forzar una sola página continua
        function forceSinglePage() {
            try {
                // Calcular la altura real del contenido
                const ticketContainer = document.querySelector('.ticket-container');
                if (ticketContainer) {
                    const contentHeight = ticketContainer.scrollHeight;
                    console.log('📏 Altura del contenido:', contentHeight + 'px');

                    // Aplicar estilos dinámicos basados en el contenido
                    const style = document.createElement('style');
                    style.textContent = `
                        @media print {
                            @page {
                                size: 80mm auto !important;
                                margin: 0 !important;
                                page-break-inside: avoid !important;
                                page-break-after: avoid !important;
                                page-break-before: avoid !important;
                            }

                            /* Forzar una sola página continua basada en el contenido */
                            body, html {
                                height: auto !important;
                                max-height: none !important;
                                min-height: auto !important;
                                overflow: visible !important;
                                page-break-inside: avoid !important;
                                page-break-after: avoid !important;
                                page-break-before: avoid !important;
                            }

                            .ticket-container {
                                height: auto !important;
                                max-height: none !important;
                                min-height: auto !important;
                                overflow: visible !important;
                                display: block !important;
                                page-break-inside: avoid !important;
                                page-break-after: avoid !important;
                                page-break-before: avoid !important;
                                /* Permitir que el contenido determine la altura */
                                white-space: nowrap;
                            }

                            /* Asegurar que todos los elementos se mantengan juntos */
                            .header, .issuer-data, .invoice-data, .receiver-data,
                            .products, .totals, .payments, .footer {
                                page-break-inside: avoid !important;
                                page-break-after: avoid !important;
                                page-break-before: avoid !important;
                                break-inside: avoid !important;
                            }
                        }
                    `;
                    document.head.appendChild(style);

                    console.log('🔄 Estilos de página continua aplicados para altura:', contentHeight + 'px');

                    // Remover el estilo después de un tiempo
                    setTimeout(() => {
                        if (style.parentNode) {
                            style.parentNode.removeChild(style);
                        }
                    }, 5000);
                }

            } catch (error) {
                console.error('Error aplicando estilos de página única:', error);
            }
        }

        function silentPrint() {
            try {
                // Forzar una sola página antes de imprimir
                forceSinglePage();

                // Agregar comandos de corte automático antes de imprimir
                addCutCommands();

                // Método 1: Intentar imprimir sin diálogo usando configuraciones específicas
                const printSettings = {
                    silent: true,
                    printBackground: true,
                    paperWidth: 80, // 80mm
                    paperHeight: 'auto', // Altura automática para una sola página
                    marginsType: 1, // Sin márgenes
                    shouldPrintBackgrounds: true,
                    shouldPrintSelectionOnly: false,
                    // Forzar una sola página
                    pageRanges: [],
                    scaleFactor: 100
                };

                // Si está disponible la API de impresión del navegador
                if (window.navigator && window.navigator.printing) {
                    window.navigator.printing.print(printSettings);
                    return true;
                }

                // Método 2: Usar execCommand si está disponible (funciona en algunos navegadores)
                if (document.execCommand) {
                    try {
                        document.execCommand('print', false, null);
                        return true;
                    } catch (e) {
                        console.log('execCommand no disponible:', e);
                    }
                }

                // Método 3: window.print() estándar con configuraciones CSS optimizadas
                window.print();
                return true;

            } catch (error) {
                console.error('Error en impresión silenciosa:', error);
                // Fallback a impresión normal
                window.print();
                return false;
            }
        }

        // Función para preparar el contenido para impresión continua
        function prepareForContinuousPrint() {
            try {
                const ticketContainer = document.querySelector('.ticket-container');
                if (ticketContainer) {
                    // Calcular la altura real del contenido
                    const contentHeight = ticketContainer.scrollHeight;
                    const viewportHeight = window.innerHeight;

                    console.log('📏 Preparando impresión continua:');
                    console.log('  - Altura del contenido:', contentHeight + 'px');
                    console.log('  - Altura del viewport:', viewportHeight + 'px');

                    // Aplicar estilos base para impresión continua
                    ticketContainer.style.height = 'auto';
                    ticketContainer.style.maxHeight = 'none';
                    ticketContainer.style.minHeight = 'auto';
                    ticketContainer.style.overflow = 'visible';

                    // Marcar el contenedor como preparado para impresión continua
                    ticketContainer.setAttribute('data-print-mode', 'continuous');

                    console.log('✅ Contenido preparado para impresión continua');
                }
            } catch (error) {
                console.error('Error preparando impresión continua:', error);
            }
        }

        window.addEventListener('load', function() {
            // Preparar el contenido para impresión continua
            prepareForContinuousPrint();

            if (autoprint) {
                setTimeout(function() {
                    if (!hasAutoprinted) {
                        hasAutoprinted = true;
                        console.log('Auto-imprimiendo ticket...');

                        // Si es auto-close, usar impresión silenciosa
                        if (autoClose) {
                            silentPrint();
                        } else {
                            printWithPrinterInfo();
                        }
                    }
                }, 500); // Reducir tiempo para impresión más rápida
            } else {
                console.log('Auto-impresión deshabilitada. Usa el botón para imprimir.');
            }
        });

        // Cerrar ventana después de imprimir si está habilitado auto_close
        window.addEventListener('afterprint', function() {
            console.log('🔄 Impresión completada, ejecutando comandos de corte...');

            // Ejecutar comandos de corte después de la impresión
            addCutCommands();

            if (autoClose) {
                console.log('Cerrando ventana automáticamente...');
                setTimeout(function() {
                    window.close();
                }, 1000);
            }
        });

        // Configurar el botón de impresión manual
        document.addEventListener('DOMContentLoaded', function() {
            const printButton = document.querySelector('.print-button');
            if (printButton) {
                printButton.onclick = function() {
                    hasAutoprinted = false; // Permitir mostrar notificación en impresión manual
                    printWithPrinterInfo();
                };
            }

            // Agregar atajo de teclado Ctrl+P
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.key === 'p') {
                    e.preventDefault();
                    hasAutoprinted = false;
                    printWithPrinterInfo();
                }
            });
        });

        // Función para imprimir desde el enlace padre (si se llama desde otra ventana)
        function triggerPrint() {
            hasAutoprinted = false;
            printWithPrinterInfo();
        }

        // Hacer la función disponible globalmente
        window.triggerPrint = triggerPrint;
    </script>
</body>
</html>
