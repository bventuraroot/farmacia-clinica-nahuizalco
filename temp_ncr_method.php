    public function ncr($id_sale)
    {
        // La nota de crédito SOLO puede venir del formulario
        if (!request()->isMethod('post') || !request()->has('productos')) {
            return redirect()->back()
                ->with('error', 'Acceso no autorizado. La nota de crédito debe crearse desde el formulario.');
        }

        // Validar que el ID de venta sea válido
        if (!$id_sale || !is_numeric($id_sale)) {
            return redirect()->back()
                ->with('error', 'ID de venta inválido.');
        }

        DB::beginTransaction();
        try {
            $request = request();

            // Obtener la venta original
            $saleOriginal = Sale::where('id', $id_sale)
                ->where('typesale', 1)
                ->where('state', 1)
                ->firstOrFail();
            $idempresa = $saleOriginal->company_id;
            $createdby = $saleOriginal->user_id;

            // Verificar modificaciones, calcular total y crear detalles
            $hayModificaciones = false;
            $totalAmount = 0;
            $productosOriginales = $saleOriginal->details->keyBy('product_id');
            $detallesModificados = [];

            foreach ($request->productos as $productoData) {
                if (!isset($productoData['incluir']) || !$productoData['incluir']) {
                    continue;
                }

                // Validar datos del producto (para NCR solo requerimos product_id y cantidad a disminuir)
                if (!isset($productoData['product_id']) || !isset($productoData['cantidad'])) {
                    DB::rollBack();
                    return redirect()->back()
                        ->with('error', 'Datos de producto incompletos. Se requiere producto y cantidad a disminuir.');
                }

                $productoOriginal = $productosOriginales->get($productoData['product_id']);
                if (!$productoOriginal) {
                    DB::rollBack();
                    return redirect()->back()
                        ->with('error', 'Producto no encontrado en la venta original.');
                }

                $cantidadOriginal = $productoOriginal->amountp;
                $precioOriginal = $productoOriginal->priceunit;
                $cantidadDisminuir = isset($productoData['cantidad']) ? (float)$productoData['cantidad'] : 0; // cantidad a disminuir
                $precioNuevo = isset($productoData['precio']) ? (float)$productoData['precio'] : $precioOriginal; // posible nuevo precio (para descuento de precio)

                // Validar entradas
                if (!is_numeric($cantidadDisminuir) || $cantidadDisminuir < 0) {
                    DB::rollBack();
                    return redirect()->back()
                        ->with('error', 'Cantidad a disminuir inválida para el producto.');
                }

                if (!is_numeric($precioNuevo) || $precioNuevo < 0) {
                    DB::rollBack();
                    return redirect()->back()
                        ->with('error', 'Precio inválido para el producto.');
                }

                // Validar que no exceda la cantidad original
                if ($cantidadDisminuir > $cantidadOriginal) {
                    DB::rollBack();
                    return redirect()->back()
                        ->with('error', 'La cantidad a disminuir no puede ser mayor a la cantidad del documento original.');
                }

                // Para NCR: el precio no puede aumentar (solo igual o menor)
                if ($precioNuevo > $precioOriginal) {
                    DB::rollBack();
                    return redirect()->back()
                        ->with('error', 'En una Nota de Crédito el precio no puede ser mayor al precio original.');
                }

                // Calcular diferencias
                $diferenciaCantidad = $cantidadDisminuir; // unidades a restar al original
                $diferenciaPrecio = max(0, $precioOriginal - $precioNuevo); // descuento unitario
                $cantidadBasePrecio = max(0, $cantidadOriginal - $cantidadDisminuir); // unidades restantes a las que aplica descuento de precio

                // Si no hay cambios en cantidad ni precio, saltar
                if ($diferenciaCantidad == 0 && $diferenciaPrecio == 0) {
                    continue;
                }

                $hayModificaciones = true;

                // Calcular subtotal diferencia total
                $subtotalDiferencia = ($diferenciaCantidad * $precioOriginal) + ($cantidadBasePrecio * $diferenciaPrecio);

                $tipoVenta = $productoData['tipo_venta'] ?? 'gravada';

                // Calcular total según el tipo de venta
                if ($tipoVenta === 'gravada') {
                    $totalAmount += $subtotalDiferencia + ($subtotalDiferencia * 0.13);
                } else {
                    // Para exenta y no_sujeta, solo se suma el subtotal sin IVA
                    $totalAmount += $subtotalDiferencia;
                }

                // Preparar datos del detalle para crear después
                $detallesModificados[] = [
                    'productoData' => $productoData,
                    'productoOriginal' => $productoOriginal,
                    'cantidadOriginal' => $cantidadOriginal,
                    'precioOriginal' => $precioOriginal,
                    'cantidadDisminuir' => $diferenciaCantidad,
                    'diferenciaPrecio' => $diferenciaPrecio,
                    'cantidadBasePrecio' => $cantidadBasePrecio,
                    'tipoVenta' => $tipoVenta
                ];
            }

            if (!$hayModificaciones) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'No se detectaron modificaciones en los productos. No se puede crear una nota de crédito sin cambios.');
            }
            // Crear la nota de crédito con solo las modificaciones
            $nfactura = new Sale();
            $nfactura->client_id = $saleOriginal->client_id;
            $nfactura->company_id = $saleOriginal->company_id;
            $nfactura->doc_related = $id_sale; // ID de la venta original
            $nfactura->typesale = 1; // Venta confirmada
            $nfactura->date = now();
            $nfactura->user_id = Auth::id();
            $nfactura->waytopay = $saleOriginal->waytopay ?? 1;
            $nfactura->state = 1; // Activa/Confirmada
            $nfactura->state_credit = 0;
            $nfactura->motivo = $request->motivo ?? 'Modificación de productos';
            $nfactura->acuenta = $saleOriginal->acuenta ?? 0;

            // Obtener el typedocument_id para notas de crédito (tipo NCR)
            $typedocumentNCR = \App\Models\Typedocument::where('type', 'NCR')
                ->where('company_id', $saleOriginal->company_id)
                ->first();

            if (!$typedocumentNCR) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'No se encontró configuración de tipo de documento NCR para esta empresa.');
            }

            $nfactura->typedocument_id = $typedocumentNCR->id;

            // Obtener y asignar el número de documento del correlativo
            $newCorr = Correlativo::join('typedocuments as tdoc', 'tdoc.type', '=', 'docs.id_tipo_doc')
                ->where('tdoc.id', '=', $typedocumentNCR->id)
                ->where('docs.id_empresa', '=', $nfactura->company_id)
                ->select('docs.actual', 'docs.id')
                ->first();

            if (!$newCorr) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'No se encontró correlativo para el tipo de documento NCR.');
            }

            $nfactura->nu_doc = $newCorr->actual;
            $nfactura->totalamount = $totalAmount;
            $nfactura->save();

            // Actualizar correlativo después de guardar la nota de crédito
            DB::table('docs')->where('id', $newCorr->id)->increment('actual');

            // Crear detalles usando los datos ya preparados
            foreach ($detallesModificados as $detalleData) {
                $productoData = $detalleData['productoData'];
                $productoOriginal = $detalleData['productoOriginal'];
                $cantidadOriginal = $detalleData['cantidadOriginal'];
                $precioOriginal = $detalleData['precioOriginal'];
                $cantidadDisminuir = $detalleData['cantidadDisminuir'];
                $diferenciaPrecio = $detalleData['diferenciaPrecio'];
                $cantidadBasePrecio = $detalleData['cantidadBasePrecio'];
                $tipoVenta = $detalleData['tipoVenta'];

                // 1) Línea por disminución de cantidad (si aplica)
                if ($cantidadDisminuir > 0) {
                    $cantidadNC = $cantidadDisminuir;
                    $precioNC = $precioOriginal;
                    $subtotal = $cantidadNC * $precioNC;

                    $detalle = new Salesdetail();
                    $detalle->sale_id = $nfactura->id;
                    $detalle->product_id = $productoData['product_id'];
                    $detalle->amountp = $cantidadNC;
                    $detalle->priceunit = $precioNC;
                    $detalle->renta = 0; // Campo requerido
                    $detalle->fee = 0; // Campo requerido
                    $detalle->feeiva = 0; // Campo requerido
                    $detalle->reserva = 0; // Campo requerido
                    $detalle->ruta = $productoOriginal->ruta ?? null;
                    $detalle->destino = $productoOriginal->destino ?? null;
                    $detalle->linea = $productoOriginal->linea ?? null;
                    $detalle->canal = $productoOriginal->canal ?? null;
                    $detalle->user_id = Auth::id();

                    if ($tipoVenta === 'gravada') {
                        $detalle->pricesale = $subtotal;
                        $detalle->detained13 = $subtotal * 0.13;
                        $detalle->detained = null; // Campo nullable
                        $detalle->exempt = 0;
                        $detalle->nosujeta = 0;
                    } elseif ($tipoVenta === 'exenta') {
                        $detalle->pricesale = 0;
                        $detalle->detained13 = 0;
                        $detalle->detained = null; // Campo nullable
                        $detalle->exempt = $subtotal;
                        $detalle->nosujeta = 0;
                    } elseif ($tipoVenta === 'no_sujeta') {
                        $detalle->pricesale = 0;
                        $detalle->detained13 = 0;
                        $detalle->detained = null; // Campo nullable
                        $detalle->exempt = 0;
                        $detalle->nosujeta = $subtotal;
                    } else {
                        // Por defecto, tratar como gravada
                        $detalle->pricesale = $subtotal;
                        $detalle->detained13 = $subtotal * 0.13;
                        $detalle->detained = null; // Campo nullable
                        $detalle->exempt = 0;
                        $detalle->nosujeta = 0;
                    }
                    $detalle->save();
                }

                // 2) Línea por disminución de precio (si aplica)
                if ($diferenciaPrecio > 0 && $cantidadBasePrecio > 0) {
                    $cantidadNC = $cantidadBasePrecio;
                    $precioNC = $diferenciaPrecio; // monto de reducción por unidad
                    $subtotal = $cantidadNC * $precioNC;

                    $detalle = new Salesdetail();
                    $detalle->sale_id = $nfactura->id;
                    $detalle->product_id = $productoData['product_id'];
                    $detalle->amountp = $cantidadNC;
                    $detalle->priceunit = $precioNC;
                    $detalle->renta = 0; // Campo requerido
                    $detalle->fee = 0; // Campo requerido
                    $detalle->feeiva = 0; // Campo requerido
                    $detalle->reserva = 0; // Campo requerido
                    $detalle->ruta = $productoOriginal->ruta ?? null;
                    $detalle->destino = $productoOriginal->destino ?? null;
                    $detalle->linea = $productoOriginal->linea ?? null;
                    $detalle->canal = $productoOriginal->canal ?? null;
                    $detalle->user_id = Auth::id();

                    if ($tipoVenta === 'gravada') {
                        $detalle->pricesale = $subtotal;
                        $detalle->detained13 = $subtotal * 0.13;
                        $detalle->detained = null; // Campo nullable
                        $detalle->exempt = 0;
                        $detalle->nosujeta = 0;
                    } elseif ($tipoVenta === 'exenta') {
                        $detalle->pricesale = 0;
                        $detalle->detained13 = 0;
                        $detalle->detained = null; // Campo nullable
                        $detalle->exempt = $subtotal;
                        $detalle->nosujeta = 0;
                    } elseif ($tipoVenta === 'no_sujeta') {
                        $detalle->pricesale = 0;
                        $detalle->detained13 = 0;
                        $detalle->detained = null; // Campo nullable
                        $detalle->exempt = 0;
                        $detalle->nosujeta = $subtotal;
                    } else {
                        // Por defecto, tratar como gravada
                        $detalle->pricesale = $subtotal;
                        $detalle->detained13 = $subtotal * 0.13;
                        $detalle->detained = null; // Campo nullable
                        $detalle->exempt = 0;
                        $detalle->nosujeta = 0;
                    }
                    $detalle->save();
                }
            }
            // Verificar si DTE está habilitado para esta empresa

            if (!Config::isDteEmissionEnabled($idempresa)) {
                DB::commit();
                if (request()->ajax()) {
                    return response('0');
                }
                return redirect()->route('credit-notes.index')
                    ->with('success', 'Nota de crédito creada exitosamente. DTE deshabilitado para esta empresa.');
            }

            // Obtener información básica de la venta original
        $qfactura = "SELECT
                        s.id id_factura,
                        s.totalamount total_venta,
                        s.company_id id_empresa,
                        s.client_id id_cliente,
                        s.user_id id_usuario,
                        clie.nit,
                        clie.email email_cliente,
                        clie.tpersona tipo_personeria,
                        CASE
                                WHEN clie.tpersona = 'N' THEN CONCAT_WS(' ', clie.firstname, clie.secondname, clie.firstlastname, clie.secondlastname)
                                WHEN clie.tpersona = 'J' THEN COALESCE(clie.name_contribuyente, '')
                            END AS nombre_cliente,
                        dte.json,
                        dte.tipoModelo,
                        dte.fhRecibido,
                        dte.codigoGeneracion,
                        dte.selloRecibido,
                        dte.tipoDte
                        FROM sales s
                        INNER JOIN clients clie ON s.client_id=clie.id
                        LEFT JOIN dte ON dte.sale_id=s.id
                        WHERE s.id = $id_sale";
        $factura = DB::select(DB::raw($qfactura));
            // Obtener información del tipo de documento NCR
        $qdoc = "SELECT
                a.id id_doc,
                a.`type` id_tipo_doc,
                docs.serie serie,
                docs.inicial inicial,
                docs.final final,
                docs.actual actual,
                docs.estado estado,
                a.company_id id_empresa,
                    " . Auth::id() . " hechopor,
                    NOW() fechacreacion,
                a.description NombreDocumento,
                    '" . Auth::user()->name . "' NombreUsuario,
                    '" . (Auth::user()->nit ?? '00000000-0') . "' docUser,
                a.codemh tipodocumento,
                a.versionjson versionJson,
                e.url_credencial,
                e.url_envio,
                e.url_invalidacion,
                e.url_contingencia,
                e.url_firmador,
                d.typeTransmission tipogeneracion,
                e.cod ambiente,
                    NOW() updated_at,
                1 aparece_ventas
                FROM typedocuments a
                INNER JOIN docs ON a.id = (SELECT t.id FROM typedocuments t WHERE t.type = docs.id_tipo_doc)
                INNER JOIN config d ON a.company_id=d.company_id
                INNER JOIN ambientes e ON d.ambiente=e.id
                    WHERE a.`type`= 'NCR' AND a.company_id = $idempresa";
        $doc = DB::select(DB::raw($qdoc));
            // Obtener detalles de la nota de crédito (solo las modificaciones)
            $detalle = $this->construirDetalleNotaCredito($nfactura->id);
        $versionJson = $doc[0]->versionJson;
        $ambiente = $doc[0]->ambiente;
        $tipoDte = $doc[0]->tipodocumento;
        $numero = $doc[0]->actual;

            // Obtener totales de la nota de crédito
            $totalesNC = $this->calcularTotalesNotaCredito($nfactura->id);

            // Construir array $totales con la estructura correcta
            $totales = [
                "totalNoSuj" => $totalesNC['nosujetas'],
                "totalExenta" => $totalesNC['exentas'],
                "totalGravada" => $totalesNC['gravadas'],
                "subTotalVentas" => $totalesNC['subtotal'],
                "totalIva" => $totalesNC['iva'],
                "totalPagar" => $totalesNC['total'],
                "totalLetras" => numeroletras($totalesNC['total']),
                "condicionOperacion" => $nfactura->waytopay ?? '01',
                "descuNoSuj" => 0,
                "descuExenta" => 0,
                "descuGravada" => 0,
                "totalDescu" => 0,
                "ivaRete1" => 0,
                "reteRenta" => 0,
                "saldoFavor" => 0
            ];

            // Construir documento fiscal para nota de crédito
            $dteOriginal = $saleOriginal->dte;

        $documento[0] = [
                "tipodocumento"             => $doc[0]->tipodocumento,
                "nu_doc"                    => $numero,
                "tipo_establecimiento"      => "1",
                "version"                   => $doc[0]->versionJson,
                "ambiente"                  => $doc[0]->ambiente,
                "tipoDteOriginal"           => $dteOriginal->tipoDte ?? '01',
                "tipoGeneracionOriginal"    => $dteOriginal->tipoModelo ?? 1,
                "codigoGeneracionOriginal"  => $dteOriginal->codigoGeneracion ?? '',
                "selloRecibidoOriginal"     => $dteOriginal->selloRecibido ?? '',
                "numeroOriginal"            => $dteOriginal->codigoGeneracion ?? '',
                "fecEmiOriginal"            => $dteOriginal ? date('Y-m-d', strtotime($dteOriginal->fhRecibido)) : date('Y-m-d'),
                "total_iva"                 => $totalesNC['iva'],
            "tipoDocumento"             => "",
                "numDocumento"              => $factura[0]->nit ?? '',
                "nombre"                    => $factura[0]->nombre_cliente ?? '',
            "versionjson"               => $doc[0]->versionJson,
                "id_empresa"                => $saleOriginal->company_id,
                "url_credencial"            => $doc[0]->url_credencial,
                "url_envio"                 => $doc[0]->url_envio,
                "url_firmador"              => $doc[0]->url_firmador,
            "nuEnvio"                   => 1,
                "condiciones"               => "1",
                "total_venta"               => $totalesNC['total'],
                "tot_gravado"               => $totalesNC['gravadas'],
                "tot_nosujeto"              => $totalesNC['nosujetas'],
                "tot_exento"                => $totalesNC['exentas'],
                "subTotalVentas"            => $totalesNC['subtotal'],
            "descuNoSuj"                => 0.00,
            "descuExenta"               => 0.00,
            "descuGravada"              => 0.00,
            "totalDescu"                => 0.00,
                "subTotal"                  => $totalesNC['subtotal'],
            "ivaPerci1"                 => 0.00,
                "ivaRete1"                  => 0.00,
            "reteRenta"                 => 0.00,
                "total_letras"              => numeroletras($totalesNC['total']),
                "totalPagar"                => $totalesNC['total'],
                "NombreUsuario"             => Auth::user()->name,
                "docUser"                   => Auth::user()->nit ?? ''
            ];
            // Obtener datos del cliente
        $qcliente = "SELECT
                                a.id id_cliente,
                            CASE
                                WHEN a.tpersona = 'N' THEN CONCAT_WS(' ', a.firstname, a.secondname, a.firstlastname, a.secondlastname)
                                WHEN a.tpersona = 'J' THEN COALESCE(a.name_contribuyente, '')
                            END AS nombre_cliente,
                                p.phone telefono_cliente,
                                a.email email_cliente,
                                c.reference direccion_cliente,
                                1 status_cliente,
                                a.created_at date_added,
                                CAST(REPLACE(REPLACE(a.ncr, '-', ''), ' ', '') AS UNSIGNED) AS ncr,
                            a.nit,
                            a.tpersona tipo_personeria,
                            g.code municipio,
                            f.code departamento,
                            a.company_id id_empresa,
                            NULL hechopor,
                            a.tipoContribuyente id_clasificacion_tributaria,
                            CASE
                                WHEN a.tipoContribuyente = 'GRA' THEN 'GRANDES CONTRIBUYENTES'
                                WHEN a.tipoContribuyente = 'MED' THEN 'MEDIANOS CONTRIBUYENTES'
                                WHEN a.tipoContribuyente = 'PEQU'  THEN 'PEQUEÑOS CONTRIBUYENTES'
                                WHEN a.tipoContribuyente = 'OTR'  THEN 'OTROS CONTRIBUYENTES'
                            END AS descripcion,
                            0 siempre_retiene,
                            1 id_tipo_contribuyente,
                            b.id giro,
                            b.code codActividad,
                            b.name descActividad,
                            a.comercial_name nombre_comercial
                        FROM clients a
                        INNER JOIN economicactivities b ON a.economicactivity_id=b.id
                        INNER JOIN addresses c ON a.address_id=c.id
                        INNER JOIN phones p ON a.phone_id=p.id
                        INNER JOIN countries d ON c.country_id=d.id
                        INNER JOIN departments f ON c.department_id=f.id
                        INNER JOIN municipalities g ON c.municipality_id=g.id
                        WHERE a.id = " . $factura[0]->id_cliente . "";
        $cliente = DB::select(DB::raw($qcliente));

            // Obtener datos del emisor (empresa)
        $queryemisor = "SELECT
                        a.nit,
                        CAST(REPLACE(REPLACE(a.ncr, '-', ''), ' ', '') AS UNSIGNED) AS ncr,
                        a.name nombre,
                        c.code codActividad,
                        c.name descActividad,
                        a.name nombreComercial,
                        a.tipoEstablecimiento,
                        f.code departamento,
                        g.code municipio,
                        d.reference direccion,
                        e.phone telefono,
                        NULL codEstableMH,
                        NULL codEstable,
                        NULL codPuntoVentaMH,
                        NULL codPuntoVenta,
                        a.email correo,
                        b.passkeyPublic clavePublicaMH,
                        b.passPrivateKey clavePrivadaMH,
                        b.passMH claveApiMH
                        FROM companies a
                        INNER JOIN config b ON a.id=b.company_id
                        INNER JOIN economicactivities c ON a.economicactivity_id=c.id
                        INNER JOIN addresses d ON a.address_id=d.id
                        INNER JOIN phones e ON a.phone_id=e.id
                        INNER JOIN departments f ON d.department_id=f.id
                        INNER JOIN municipalities g ON d.municipality_id=g.id
                            WHERE a.id=" . $saleOriginal->company_id . "";
        $emisor = DB::select(DB::raw($queryemisor));

            // Construir comprobante para envío a Hacienda
        $comprobante = [
            "emisor" => $emisor,
            "documento" => $documento,
            "detalle" => $detalle,
            "totales" => $totales,
            "cliente" => $cliente
        ];

            // Enviar a Hacienda
        $respuesta = $this->Enviar_Hacienda($comprobante, "05");
        if ($respuesta["codEstado"] == "03") {
                // CREAR DTE CON ESTADO RECHAZADO Y REGISTRAR ERROR
                $dtecreate = $this->crearDteConError($doc, $emisor, $respuesta, $comprobante, $nfactura, $createdby);
                // REGISTRAR ERROR EN LA TABLA dte_errors
                $this->registrarErrorDte($dtecreate, 'hacienda', 'HACIENDA_REJECTED', $respuesta["descripcionMsg"] ?? 'Documento rechazado por Hacienda', [
                    'codigoMsg' => $respuesta["codigoMsg"] ?? null,
                    'observacionesMsg' => $respuesta["observacionesMsg"] ?? null,
                    'sale_id' => $nfactura->id
                ]);

                // Guardar JSON con información de rechazo en la tabla sales
                $comprobante["json"] = $respuesta;
                $nfactura->json = json_encode($comprobante);
                $nfactura->save();

                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Error al enviar a Hacienda: ' . ($respuesta["descripcionMsg"] ?? 'Documento rechazado'));
            }
            //dd($respuesta);
        $comprobante["json"] = $respuesta;
            // Crear registro DTE
            $dte = new \App\Models\Dte();
            $dte->versionJson = $doc[0]->versionJson;
            $dte->ambiente_id = $doc[0]->ambiente;
            $dte->tipoDte = $doc[0]->tipodocumento;
            $dte->tipoModelo = $doc[0]->tipogeneracion;
            $dte->tipoTransmision = 1;
            $dte->tipoContingencia = "null";
            $dte->idContingencia = "null";
            $dte->nameTable = 'Sales';
            $dte->company_id = $nfactura->company_id;
            $dte->company_name = $emisor[0]->nombreComercial;
            $dte->id_doc = $respuesta["identificacion"]["numeroControl"];
            $dte->codTransaction = "01";
            $dte->desTransaction = "Emision";
            $dte->type_document = $doc[0]->tipodocumento;
            $dte->id_doc_Ref1 = "null";
            $dte->id_doc_Ref2 = "null";
            $dte->type_invalidacion = "null";
            $dte->codEstado = $respuesta["codEstado"];
            $dte->Estado = $respuesta["estado"];
            $dte->codigoGeneracion = $respuesta["codigoGeneracion"];
            $dte->selloRecibido = $respuesta["selloRecibido"];
            $dte->fhRecibido = $respuesta["fhRecibido"];
            $dte->estadoHacienda = $respuesta["estadoHacienda"];
            $dte->json = json_encode($comprobante);
            $dte->nSends = $respuesta["nuEnvios"];
            $dte->codeMessage = $respuesta["codigoMsg"];
            $dte->claMessage = $respuesta["clasificaMsg"];
            $dte->descriptionMessage = $respuesta["descripcionMsg"];
            $dte->detailsMessage = $respuesta["observacionesMsg"];
            $dte->sale_id = $nfactura->id;
            $dte->created_by = $doc[0]->NombreUsuario;
            $dte->save();

            $nfactura->codigoGeneracion = $respuesta["codigoGeneracion"];

            // Agregar el codigoGeneracion al JSON antes de guardarlo
            //$comprobante["json"] = $respuesta;
            $nfactura->json = json_encode($comprobante);
            $nfactura->save();

            // El correlativo ya fue actualizado arriba

            DB::commit();
            if (request()->ajax()) {
                return response('1');
            }
            return redirect()->route('sale.index')
                ->with('success', 'Nota de crédito creada y enviada a Hacienda exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creando nota de crédito: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            // Para debugging temporal, mostrar el error completo
            if (config('app.debug')) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Error al procesar la nota de crédito: ' . $e->getMessage() . ' en línea ' . $e->getLine());
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al procesar la nota de crédito. Revisa los logs para más detalles.');
        }
    }

    /**
     * Calcular totales de la nota de crédito
     */
    private function calcularTotalesNotaCredito($notaCreditoId): array
    {
        $totales = Salesdetail::where('sale_id', $notaCreditoId)
            ->selectRaw('
                SUM(pricesale) as gravadas,
                SUM(exempt) as exentas,
                SUM(nosujeta) as nosujetas,
                SUM(detained13) as iva,
                SUM(pricesale + exempt + nosujeta) as subtotal
            ')
            ->first();

        $total = $totales->subtotal + $totales->iva;

        return [
            'gravadas' => (float)$totales->gravadas,
            'exentas' => (float)$totales->exentas,
            'nosujetas' => (float)$totales->nosujetas,
            'iva' => (float)$totales->iva,
            'subtotal' => (float)$totales->subtotal,
            'total' => (float)$total
        ];
    }

    /**
     * Construir detalle fiscal para la nota de crédito
     */
    private function construirDetalleNotaCredito($notaCreditoId): array
    {
        $queryDetalle = "SELECT
                        *,
                        det.id id_factura_det,
                        det.sale_id id_factura,
                        det.product_id id_producto,
                        CASE
                            WHEN det.description IS NOT NULL AND det.description != '' THEN det.description
                            ELSE pro.description
                        END AS descripcion,
                        det.amountp cantidad,
                        det.priceunit precio_unitario,
                        det.nosujeta no_sujetas,
                        det.exempt exentas,
                        det.pricesale gravadas,
                        det.detained13 iva,
                        0.00 no_imponible,
                        sa.company_id id_empresa,
                        CASE
                                WHEN pro.`type` = 'tercero' THEN 'T'
                                WHEN pro.`type` = 'directo' THEN 'D'
                            END AS tipo_producto,
                        0.00 porcentaje_descuento,
                        0.00 descuento,
                        det.created_at,
                        det.updated_at
                        FROM salesdetails det
                        INNER JOIN sales sa ON det.sale_id=sa.id
                        INNER JOIN products pro ON det.product_id=pro.id
                        WHERE det.sale_id = $notaCreditoId";

        return DB::select(DB::raw($queryDetalle));
    }
