<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pos\DashboardController;
use App\Http\Controllers\Pos\ProductoLocalController;
use App\Http\Controllers\Pos\LoteLocalController;
use App\Http\Controllers\Pos\MetodoPagoController;
use App\Http\Controllers\Pos\ClienteController;
use App\Http\Controllers\Pos\StockLocalController;
use App\Http\Controllers\Pos\VentaFisicaController;
use App\Http\Controllers\Pos\DetalleVentaController;
use App\Http\Controllers\Pos\UserController;

Route::prefix('pos')->name('pos.')->middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('sedes', \App\Http\Controllers\Pos\SedeController::class)->except(['show']);
    Route::resource('productos', ProductoLocalController::class)->except(['edit', 'update']);
    Route::get('productos/{producto}/edit', [ProductoLocalController::class, 'edit'])->name('productos.edit');
    Route::put('productos/{producto}', [ProductoLocalController::class, 'update'])->name('productos.update');
    Route::resource('lotes', LoteLocalController::class);
    Route::resource('metodos-pago', MetodoPagoController::class)->except(['show']);
    Route::resource('clientes', ClienteController::class)->except(['show']);

    Route::get('stock', [StockLocalController::class, 'index'])->name('stock.index');
    Route::post('stock/{lote}/retirar', [LoteLocalController::class, 'retirar'])->name('stock.retirar');

    Route::get('ventas', [VentaFisicaController::class, 'index'])->name('ventas.index');
    Route::get('ventas/crear', [VentaFisicaController::class, 'create'])->name('ventas.create');
    Route::post('ventas', [VentaFisicaController::class, 'store'])->name('ventas.store');
    Route::get('ventas/{venta}', [VentaFisicaController::class, 'show'])->name('ventas.show');

    Route::get('detalle-ventas', [DetalleVentaController::class, 'index'])->name('detalle-ventas.index');

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/crear', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});
