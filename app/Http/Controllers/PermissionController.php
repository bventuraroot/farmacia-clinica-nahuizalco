<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.users.permissions.index');
    }

    public function getpermission()
    {
        $permission['data'] = Permission::all();
        //dd($permission['data']);
        foreach($permission['data'] as $index => $value){
                @$permission['data'][$index]['roles']=[];
                $idpermissiondata = @$value->id;
                $roles = "SELECT b.name AS rolename FROM role_has_permissions a
                INNER JOIN roles b ON a.role_id=b.id
                WHERE a.permission_id='$idpermissiondata'
                GROUP BY b.name";
                $result = DB::select(DB::raw($roles));
                foreach($result as $index2 => $value2){
                    @$permission['data'][$index]['roles']=$result;
                }
        }
        //dd($permission['data']);
        return response()->json($permission);
    }

    public function getmenujson(){
        $userId=auth()->user()->id;
        $rolhasuser = "SELECT
                a.model_id UserID,
                b.id Rolid,
                b.name Rol,
                d.id PermisoID,
                d.name Permiso
                FROM model_has_roles a
                INNER JOIN roles b ON a.role_id=b.id
                LEFT JOIN role_has_permissions c ON b.id=c.role_id
                LEFT JOIN permissions d ON c.permission_id=d.id
                WHERE model_id=$userId";
        $result = DB::select(DB::raw($rolhasuser));
        //dd($result);

        $menu = [
            [
                "url" => "/",
                "name" => "Home",
                "icon" => "menu-icon fa-solid fa-home",
                "slug" => "dashboard"
            ],
            [
                "name" => "Administracion",
                "icon" => "menu-icon fa-solid fa-shield",
                "slug" => "user.index",
                "badge" => ["primary", "3"],
                "submenu" => [
                    [
                        "url" => "/user/index",
                        "name" => "Usuarios",
                        "slug" => "user.index"
                    ],
                    [
                        "url" => "/rol/index",
                        "name" => "Roles",
                        "slug" => "user.rol.index"
                    ],
                    [
                        "url" => "/permission/index",
                        "name" => "Permisos",
                        "slug" => "user.permission.index"
                    ]
                ]
            ],
            [
                "url" => "/company/index",
                "name" => "Empresas",
                "icon" => "menu-icon fa-solid fa-id-badge",
                "slug" => "company.index"
            ],
            [
                "url" => "/client/index",
                "name" => "Clientes",
                "icon" => "menu-icon fa-solid fa-user-plus",
                "slug" => "client.index"
            ],
            [
                "name" => "Produccion",
                "icon" => "menu-icon fa-solid fa-feed",
                "slug" => "user.index",
                "badge" => ["primary", "3"],
                "submenu" => [
                    [
                        "url" => "/provider/index",
                        "name" => "Proveedores",
                        "slug" => "provider.index"
                    ],
                    [
                        "url" => "/marcas/index",
                        "name" => "Marcas",
                        "slug" => "marcas.index"
                    ],
                    [
                        "url" => "/product/index",
                        "name" => "Productos",
                        "slug" => "product.index"
                    ]

                ]
            ],
            [
                "url" => "/presales/index",
                "name" => "Pre-ventas",
                "icon" => "menu-icon fa-solid fa-cart-shopping",
                "slug" => "presales.index"
            ],
            [
                "url" => "/sale/index",
                "name" => "Ventas",
                "icon" => "menu-icon fa-solid fa-dollar",
                "slug" => "sale.index"
            ],
            [
                "url" => "/inventory",
                "name" => "Inventario",
                "icon" => "menu-icon fa-solid fa-boxes-packing",
                "slug" => "inventory.index"
            ],
            [
                "url" => "/purchase/index",
                "name" => "Compras",
                "icon" => "menu-icon fa-solid fa-truck",
                "slug" => "purchase.index"
            ],
            [
                "url" => "/credit/index",
                "name" => "Creditos",
                "icon" => "menu-icon fa-solid fa-credit-card",
                "slug" => "credit.index"
            ],
            [
                "url" => "/cotizaciones/index",
                "name" => "Cotizaciones",
                "icon" => "menu-icon fa-solid fa-file-invoice",
                "slug" => "cotizaciones.index"
            ],
            [
                "url" => "/ai-chat/",
                "name" => "Chat IA",
                "icon" => "menu-icon fa-solid fa-robot",
                "slug" => "ai-chat"
            ],
            [
                "url" => "/backups",
                "name" => "Respaldos",
                "icon" => "menu-icon fa-solid fa-database",
                "slug" => "backups.index"
            ],
            [
                "url" => "/report/index",
                "name" => "Reportes",
                "icon" => "menu-icon fa-solid fa-line-chart",
                "slug" => "report.index",
                "badge" => ["primary", "20"],
                "submenu" => [
                    [
                        "url" => "/report/sales",
                        "name" => "Ventas",
                        "slug" => "report.sales"
                    ],
                    [
                        "url" => "/report/sales-by-client",
                        "name" => "Ventas por Clientes",
                        "slug" => "report.sales-by-client"
                    ],
                    [
                        "url" => "/report/contribuyentes",
                        "name" => "Ventas Contribuyentes",
                        "slug" => "report.contribuyentes"
                    ],
                    [
                        "url" => "/report/consumidor",
                        "name" => "Ventas Consumidor",
                        "slug" => "report.consumidor"
                    ],
                    [
                        "url" => "/report/bookpurchases",
                        "name" => "Libro de Compras",
                        "slug" => "report.bookpurchases"
                    ],
                    [
                        "url" => "/report/inventory",
                        "name" => "Inventario",
                        "slug" => "report.inventory"
                    ],
                    [
                        "url" => "/report/reportyear",
                        "name" => "Ventas y compras por año",
                        "slug" => "report.reportyear"
                    ],
                    [
                        "url" => "/report/sales-by-provider",
                        "name" => "Ventas por Proveedor",
                        "slug" => "report.sales-by-provider"
                    ],
                    [
                        "url" => "/report/sales-analysis",
                        "name" => "Análisis General de Ventas",
                        "slug" => "report.sales-analysis"
                    ],
                    [
                        "url" => "/report/sales-by-product",
                        "name" => "Ventas por Producto",
                        "slug" => "report.sales-by-product"
                    ],
                    [
                        "url" => "/report/inventory-by-category",
                        "name" => "Inventario por Categoría",
                        "slug" => "report.inventory-by-category"
                    ],
                    [
                        "url" => "/report/inventory-by-provider",
                        "name" => "Inventario por Proveedor",
                        "slug" => "report.inventory-by-provider"
                    ],
                    //[
                       // "url" => "/report/inventory-movements",
                        //"name" => "Movimientos de Inventario",
                        //"slug" => "report.inventory-movements"
                    //],
                    [
                        "url" => "/report/inventory-kardex",
                        "name" => "Kardex (por producto)",
                        "slug" => "report.inventory-kardex"
                    ]
                ]
            ],
            [
                "url" => "/factmh/index",
                "name" => "Administracion DTE",
                "icon" => "menu-icon fa-solid fa-satellite-dish",
                "slug" => "factmh.index",
                "badge" => ["primary", "9"],
                "submenu" => [
                    [
                        "url" => "/factmh/dashboard",
                        "name" => "Dashboard DTE",
                        "slug" => "factmh.dashboard"
                    ],
                    [
                        "url" => "/factmh/contingencias",
                        "name" => "Contingencias (Legacy)",
                        "slug" => "factmh.contingencias"
                    ],
                    [
                        "url" => "/dte/contingencias",
                        "name" => "Gestión de Contingencias DTE",
                        "slug" => "dte.contingencias"
                    ],
                    [
                        "url" => "/correlativos/",
                        "name" => "Gestión de Correlativos",
                        "slug" => "correlativos.index"
                    ],
                    [
                        "url" => "/dte/errores",
                        "name" => "Gestión de Errores",
                        "slug" => "dte.errores"
                    ],
                    [
                        "url" => "/dte/test_crt",
                        "name" => "Test Certificado",
                        "slug" => "dte.test_crt"
                    ],
                    [
                        "url" => "/firmador/test",
                        "name" => "Test Firmador",
                        "slug" => "firmador.test"
                    ],
                    [
                        "url" => "/config/index",
                        "name" => "Configuraciones Ambiente",
                        "slug" => "config.index"
                    ]
                ]
            ]
        ];

        $filteredMenu = [];
        //dd($result);
        if(@$result[0]->Rolid!=1){

        // Agregar el primer elemento "Home" directamente
        if (isset($menu[0])) {
            $filteredMenu[] = $menu[0];
        }

        foreach ($menu as $index => $menuItem) {
            // Saltar el primer elemento (ya agregado)
            if ($index === 0) {
                continue;
            }

            // Verifica si el usuario tiene acceso a la sección principal (slug)
            if (in_array($menuItem['slug'], array_column($result, 'Permiso'))) {
                // Clona el menú principal
                $filteredItem = $menuItem;

                // Filtra el submenu si existe (incluyendo submenús anidados)
                if (isset($menuItem['submenu'])) {
                    $filteredSubmenu = [];
                    foreach ($menuItem['submenu'] as $submenuItem) {
                        // Verifica el permiso para cada subelemento del menú
                        if (in_array($submenuItem['slug'], array_column($result, 'Permiso'))) {
                            $filteredSubmenuItem = $submenuItem;

                            // Maneja submenús anidados (subnivel)
                            if (isset($submenuItem['submenu'])) {
                                $filteredNestedSubmenu = [];
                                foreach ($submenuItem['submenu'] as $nestedSubmenuItem) {
                                    if (in_array($nestedSubmenuItem['slug'], array_column($result, 'Permiso'))) {
                                        $filteredNestedSubmenu[] = $nestedSubmenuItem;
                                    }
                                }
                                // Solo añade el submenu anidado si hay permisos
                                if (!empty($filteredNestedSubmenu)) {
                                    $filteredSubmenuItem['submenu'] = $filteredNestedSubmenu;
                                } else {
                                    // Si no hay permisos para submenús, quita la propiedad submenu
                                    unset($filteredSubmenuItem['submenu']);
                                }
                            }

                            $filteredSubmenu[] = $filteredSubmenuItem;
                        }
                    }
                    // Solo añade el submenu si hay permisos
                    if (!empty($filteredSubmenu)) {
                        $filteredItem['submenu'] = $filteredSubmenu;
                    }
                }

                $filteredMenu[] = $filteredItem;
            }
        }

        // Encerrar todo en la palabra "menu"
$finalMenu = ['menu' => $filteredMenu];
$menuJson = json_encode($finalMenu);
//dd($menuJson);
}else {
    $finalMenu = ['menu' => $menu];
    $menuJson = json_encode($finalMenu);;
}
return response()->json($menuJson);
    }

    public function getpermissionjson(){
        $userId=auth()->user()->id;
        $rolhasuser = "SELECT
                a.model_id UserID,
                b.id Rolid,
                b.name Rol,
                d.id PermisoID,
                d.name Permiso
                FROM model_has_roles a
                INNER JOIN roles b ON a.role_id=b.id
                LEFT JOIN role_has_permissions c ON b.id=c.role_id
                LEFT JOIN permissions d ON c.permission_id=d.id
                WHERE model_id=$userId";
        $result = DB::select(DB::raw($rolhasuser));
        return response()->json($result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Permission::create(['name' => $request->modalPermissionName]);
        return redirect()->route('permission.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function show(Permission $permission)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function edit(Permission $permission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Permission $permission)
    {
        $permission = Permission::find($request->editPermissionid);
        $permission->name = $request->editPermissionName;
        $permission->save();
        return redirect()->route('permission.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $permission = Permission::find(base64_decode($id));
        $permission->delete();
        return response()->json(array(
            "res" => "1"
        ));
    }

    /**
     * Crear permisos para el módulo de correlativos
     */
    public function createCorrelativosPermissions()
    {
        try {
            $permissions = [
                'correlativos.index' => 'Ver lista de correlativos',
                'correlativos.create' => 'Crear correlativos',
                'correlativos.edit' => 'Editar correlativos',
                'correlativos.destroy' => 'Eliminar correlativos',
                'correlativos.estadisticas' => 'Ver estadísticas de correlativos',
                'correlativos.reactivar' => 'Reactivar correlativos agotados',
                'correlativos.cambiar-estado' => 'Cambiar estado de correlativos',
                'correlativos.api.siguiente-numero' => 'API: Obtener siguiente número',
                'correlativos.api.validar-disponibilidad' => 'API: Validar disponibilidad'
            ];

            $createdPermissions = [];
            $existingPermissions = [];

            foreach ($permissions as $name => $description) {
                // Verificar si el permiso ya existe
                $existingPermission = Permission::where('name', $name)->first();

                if (!$existingPermission) {
                    Permission::create([
                        'name' => $name,
                        'guard_name' => 'web'
                    ]);
                    $createdPermissions[] = $name;
                } else {
                    $existingPermissions[] = $name;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Permisos de correlativos procesados correctamente',
                'created' => $createdPermissions,
                'existing' => $existingPermissions,
                'total_created' => count($createdPermissions),
                'total_existing' => count($existingPermissions)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear permisos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Asignar permisos de correlativos a un rol específico
     */
    public function assignCorrelativosPermissions(Request $request)
    {
        try {
            $request->validate([
                'role_id' => 'required|exists:roles,id',
                'permissions' => 'array'
            ]);

            $role = Role::findById($request->role_id);

            // Obtener todos los permisos de correlativos si no se especifican
            if (empty($request->permissions)) {
                $correlativosPermissions = Permission::where('name', 'like', 'correlativos.%')->pluck('name')->toArray();
            } else {
                $correlativosPermissions = $request->permissions;
            }

            // Asignar permisos al rol
            $role->syncPermissions(array_merge($role->permissions->pluck('name')->toArray(), $correlativosPermissions));

            return response()->json([
                'success' => true,
                'message' => 'Permisos asignados correctamente al rol: ' . $role->name,
                'assigned_permissions' => $correlativosPermissions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar permisos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear permisos para el módulo de contingencias DTE
     */
    public function createContingenciasPermissions()
    {
        try {
            $permissions = [
                'dte.contingencias' => 'Ver gestión de contingencias DTE',
                'dte.contingencias.create' => 'Crear contingencias',
                'dte.contingencias.store' => 'Guardar contingencias',
                'dte.contingencias.edit' => 'Editar contingencias',
                'dte.contingencias.update' => 'Actualizar contingencias',
                'dte.contingencias.destroy' => 'Eliminar contingencias',
                'dte.contingencias.autorizar' => 'Autorizar contingencias',
                'dte.contingencias.aprobar' => 'Aprobar contingencias',
                'dte.contingencias.activar' => 'Activar contingencias',
                'dte.contingencias.rechazar' => 'Rechazar contingencias',
                'dte.contingencias.estadisticas' => 'Ver estadísticas de contingencias',
                'dte.contingencias.exportar' => 'Exportar reportes de contingencias',
                'dte.contingencias.automaticas' => 'Gestionar contingencias automáticas',
                'dte.contingencias.alertas' => 'Configurar alertas de contingencias',
                'factmh.contingencias' => 'Ver contingencias legacy',
                'factmh.store' => 'Crear contingencias legacy',
                'factmh.autoriza_contingencia' => 'Autorizar contingencias legacy',
                'factmh.muestra_lote' => 'Ver lote de contingencias'
            ];

            $createdPermissions = [];
            $existingPermissions = [];

            foreach ($permissions as $name => $description) {
                // Verificar si el permiso ya existe
                $existingPermission = Permission::where('name', $name)->first();

                if (!$existingPermission) {
                    Permission::create([
                        'name' => $name,
                        'guard_name' => 'web'
                    ]);
                    $createdPermissions[] = $name;
                } else {
                    $existingPermissions[] = $name;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Permisos de contingencias DTE procesados correctamente',
                'created' => $createdPermissions,
                'existing' => $existingPermissions,
                'total_created' => count($createdPermissions),
                'total_existing' => count($existingPermissions)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear permisos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Asignar permisos de contingencias DTE a un rol específico
     */
    public function assignContingenciasPermissions(Request $request)
    {
        try {
            $request->validate([
                'role_id' => 'required|exists:roles,id',
                'permissions' => 'array'
            ]);

            $role = Role::findById($request->role_id);

            // Obtener todos los permisos de contingencias si no se especifican
            if (empty($request->permissions)) {
                $contingenciasPermissions = Permission::where('name', 'like', 'dte.contingencias.%')
                    ->orWhere('name', 'like', 'factmh.%')
                    ->pluck('name')->toArray();
            } else {
                $contingenciasPermissions = $request->permissions;
            }

            // Asignar permisos al rol
            $role->syncPermissions(array_merge($role->permissions->pluck('name')->toArray(), $contingenciasPermissions));

            return response()->json([
                'success' => true,
                'message' => 'Permisos de contingencias asignados correctamente al rol: ' . $role->name,
                'assigned_permissions' => $contingenciasPermissions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar permisos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear permisos para el módulo de reportes
     */
    public function createReportsPermissions()
    {
        try {
            $permissions = [
                // Permisos existentes de reportes
                'report.sales' => 'Ver reporte de ventas',
                'report.purchases' => 'Ver reporte de compras',
                'report.contribuyentes' => 'Ver reporte de contribuyentes',
                'report.consumidor' => 'Ver reporte de consumidor final',
                'report.bookpurchases' => 'Ver libro de compras',
                'report.reportyear' => 'Ver reporte anual',
                'report.inventory' => 'Ver reporte de inventario',
                'report.sales-by-client' => 'Ver reporte de ventas por cliente',

                // Nuevos permisos de reportes
                'report.sales-by-provider' => 'Ver reporte de ventas por proveedor',
                'report.sales-analysis' => 'Ver análisis general de ventas',
                'report.sales-by-product' => 'Ver reporte de ventas por producto',
                'report.sales-by-category' => 'Ver reporte de ventas por categoría',
                'report.inventory-by-category' => 'Ver reporte de inventario por categoría',
                'report.inventory-by-provider' => 'Ver reporte de inventario por proveedor',

                // Nuevos reportes de inventario (Noviembre 2024)
                'report.inventory-movements' => 'Ver reporte de movimientos de inventario',
                'report.inventory-kardex' => 'Ver Kardex de inventario por producto',

                // Permisos de búsqueda
                'report.sales-by-provider-search' => 'Buscar en reporte de ventas por proveedor',
                'report.sales-analysis-search' => 'Buscar en análisis general de ventas',
                'report.sales-by-product-search' => 'Buscar en reporte de ventas por producto',
                'report.sales-by-category-search' => 'Buscar en reporte de ventas por categoría',
                'report.inventory-search' => 'Buscar en reporte de inventario',
                'report.inventory-movements-search' => 'Buscar en reporte de movimientos de inventario',

                // Permisos de exportación
                'report.export-excel' => 'Exportar reportes a Excel',
                'report.export-pdf' => 'Exportar reportes a PDF'
            ];

            $createdPermissions = [];
            $existingPermissions = [];

            foreach ($permissions as $name => $description) {
                // Verificar si el permiso ya existe
                $existingPermission = Permission::where('name', $name)->first();

                if (!$existingPermission) {
                    Permission::create([
                        'name' => $name,
                        'guard_name' => 'web'
                    ]);
                    $createdPermissions[] = $name;
                } else {
                    $existingPermissions[] = $name;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Permisos de reportes procesados correctamente',
                'created' => $createdPermissions,
                'existing' => $existingPermissions,
                'total_created' => count($createdPermissions),
                'total_existing' => count($existingPermissions)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear permisos de reportes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Asignar permisos de reportes a un rol específico
     */
    public function assignReportsPermissions(Request $request)
    {
        try {
            $request->validate([
                'role_id' => 'required|exists:roles,id',
                'permissions' => 'array'
            ]);

            $role = Role::findById($request->role_id);

            // Obtener todos los permisos de reportes si no se especifican
            if (empty($request->permissions)) {
                $reportsPermissions = Permission::where('name', 'like', 'report.%')->pluck('name')->toArray();
            } else {
                $reportsPermissions = $request->permissions;
            }

            // Asignar permisos al rol
            $role->syncPermissions(array_merge($role->permissions->pluck('name')->toArray(), $reportsPermissions));

            return response()->json([
                'success' => true,
                'message' => 'Permisos de reportes asignados correctamente al rol: ' . $role->name,
                'assigned_permissions' => $reportsPermissions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar permisos de reportes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear permisos para el módulo de respaldos
     */
    public function createBackupsPermissions()
    {
        try {
            $permissions = [
                'backups.index' => 'Ver lista de respaldos',
                'backups.create' => 'Crear respaldos de base de datos',
                'backups.download' => 'Descargar respaldos',
                'backups.destroy' => 'Eliminar respaldos',
                'backups.restore' => 'Restaurar respaldos',
                'backups.list' => 'Listar respaldos disponibles',
                'backups.stats' => 'Ver estadísticas de respaldos',
                'backups.scheduled' => 'Gestionar respaldos programados',
                'backups.automated' => 'Configurar respaldos automáticos',
                'backups.compression' => 'Configurar compresión de respaldos',
                'backups.retention' => 'Gestionar política de retención',
                'backups.notifications' => 'Configurar notificaciones de respaldos'
            ];

            $createdPermissions = [];
            $existingPermissions = [];

            foreach ($permissions as $name => $description) {
                // Verificar si el permiso ya existe
                $existingPermission = Permission::where('name', $name)->first();

                if (!$existingPermission) {
                    Permission::create([
                        'name' => $name,
                        'guard_name' => 'web'
                    ]);
                    $createdPermissions[] = $name;
                } else {
                    $existingPermissions[] = $name;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Permisos de respaldos procesados correctamente',
                'created' => $createdPermissions,
                'existing' => $existingPermissions,
                'total_created' => count($createdPermissions),
                'total_existing' => count($existingPermissions)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear permisos de respaldos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Asignar permisos de respaldos a un rol específico
     */
    public function assignBackupsPermissions(Request $request)
    {
        try {
            $request->validate([
                'role_id' => 'required|exists:roles,id',
                'permissions' => 'array'
            ]);

            $role = Role::findById($request->role_id);

            // Obtener todos los permisos de respaldos si no se especifican
            if (empty($request->permissions)) {
                $backupsPermissions = Permission::where('name', 'like', 'backups.%')->pluck('name')->toArray();
            } else {
                $backupsPermissions = $request->permissions;
            }

            // Asignar permisos al rol
            $role->syncPermissions(array_merge($role->permissions->pluck('name')->toArray(), $backupsPermissions));

            return response()->json([
                'success' => true,
                'message' => 'Permisos de respaldos asignados correctamente al rol: ' . $role->name,
                'assigned_permissions' => $backupsPermissions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar permisos de respaldos: ' . $e->getMessage()
            ], 500);
        }
    }
}
