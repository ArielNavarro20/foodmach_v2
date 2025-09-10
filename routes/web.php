<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    HomeController,
    PedidoController,
    AdminController,
    RecomendarController,
    OpinionController,
    MenuController,
    PlanController,
    PlanSemanalController,
    IngredienteController
};
use App\Http\Middleware\AdminOnly;

/* 
   Home y preferencias
*/
Route::get('/', [HomeController::class,'index'])->name('home');
Route::post('/preferencias', [HomeController::class,'guardarPreferencias'])->name('preferencias.guardar');

/* Historial en Home */
Route::post('/historial/ignorar', [HomeController::class,'ignorarHistorial'])->name('historial.ignorar');
Route::post('/historial/usar',     [HomeController::class,'usarHistorial'])->name('historial.usar');
Route::post('/historial/limpiar',  [HomeController::class,'limpiarHistorial'])->name('historial.limpiar');

/* 
   Auth
*/
Route::get('/login',  [AuthController::class,'form'])->name('login.form');
Route::post('/login', [AuthController::class,'login'])->name('login.do');
Route::post('/logout',[AuthController::class,'logout'])->name('logout');

/* 
   Recomendaciones
 */
Route::get('/recomendar', [RecomendarController::class,'index'])->name('recomendar');

/* 
   Pedidos del cleitne
*/
Route::post('/comprar',     [PedidoController::class,'comprar'])->name('pedido.comprar');
Route::get('/mis-pedidos',  [PedidoController::class,'misPedidos'])->name('mis.pedidos');
Route::get('/boleta/{id?}', [PedidoController::class,'boleta'])->name('boleta');

/* 
   Opiniones de usuario
*/
Route::get('/opiniones',  [OpinionController::class,'index'])->name('opiniones.index');
Route::get('/opinar',     [OpinionController::class,'form'])->name('opiniones.form')->middleware('auth');
Route::post('/opiniones', [OpinionController::class,'store'])->name('opiniones.store')->middleware('auth');

/* 
   menú avanzado
*/
Route::get('/builder', [MenuController::class,'builderForm'])->name('builder.form')->middleware('auth');
Route::post('/builder',[MenuController::class,'builderCrear'])->name('builder.crear')->middleware('auth');

/* 
   lo del Plan semanal
 */
Route::middleware('auth')->group(function () {
    Route::get('/plan',       [PlanController::class,'ver'])->name('plan.ver');
    Route::get('/plan/print', [PlanController::class,'imprimir'])->name('plan.print');

    
    Route::get('/plan-semanal',     [PlanSemanalController::class,'ver'])->name('plan2.ver');
    Route::get('/plan-semanal/pdf', [PlanSemanalController::class,'pdf'])->name('plan2.pdf');
});

/* 
   ADMIN CRUD
    */
Route::middleware([AdminOnly::class])->group(function () {
    Route::get('/admin', [AdminController::class,'index'])->name('admin.index');

    // Pedidos
    Route::get('/admin/pedidos/{id}/editar', [AdminController::class,'pedidoEdit'])->name('admin.pedido.edit');
    Route::put('/admin/pedidos/{id}',        [AdminController::class,'pedidoUpdate'])->name('admin.pedido.update');
    Route::delete('/admin/pedidos/{id}',     [AdminController::class,'eliminarPedido'])->name('admin.pedido.eliminar');

    // Restaurantes
    Route::get('/admin/restaurantes/crear',       [AdminController::class,'restaurantesCreate'])->name('admin.rest.create');
    Route::post('/admin/restaurantes',            [AdminController::class,'restaurantesStore'])->name('admin.rest.store');
    Route::get('/admin/restaurantes/{id}/editar', [AdminController::class,'restaurantesEdit'])->name('admin.rest.edit');
    Route::put('/admin/restaurantes/{id}',        [AdminController::class,'restaurantesUpdate'])->name('admin.rest.update');
    Route::delete('/admin/restaurantes/{id}',     [AdminController::class,'eliminarRestaurante'])->name('admin.rest.eliminar');

    // Menús
    Route::get('/admin/menus/crear',       [AdminController::class,'menusCreate'])->name('admin.menu.create');
    Route::post('/admin/menus',            [AdminController::class,'menusStore'])->name('admin.menu.store');
    Route::get('/admin/menus/{id}/editar', [AdminController::class,'menusEdit'])->name('admin.menu.edit');
    Route::put('/admin/menus/{id}',        [AdminController::class,'menusUpdate'])->name('admin.menu.update');
    Route::delete('/admin/menus/{id}',     [AdminController::class,'eliminarMenu'])->name('admin.menu.eliminar');

    // Ingredientes nombres admin.ingredientes.*
    Route::resource('/admin/ingredientes', IngredienteController::class)
        ->except(['show'])
        ->names([
            'index'   => 'admin.ingredientes.index',
            'create'  => 'admin.ingredientes.create',
            'store'   => 'admin.ingredientes.store',
            'edit'    => 'admin.ingredientes.edit',
            'update'  => 'admin.ingredientes.update',
            'destroy' => 'admin.ingredientes.destroy',
        ]);

    // Opiniones
    Route::get('/admin/opiniones/{id}/editar', [AdminController::class,'opinionesEdit'])->name('admin.opinion.edit');
    Route::put('/admin/opiniones/{id}',        [AdminController::class,'opinionesUpdate'])->name('admin.opinion.update');
    Route::delete('/admin/opiniones/{id}',     [AdminController::class,'eliminarOpinion'])->name('admin.opinion.eliminar');
});
