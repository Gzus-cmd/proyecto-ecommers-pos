<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pos\DashboardController;
use App\Http\Controllers\Pos\SedeController;
use App\Http\Controllers\Pos\ProductoLocalController;
use App\Http\Controllers\Pos\LoteLocalController;
use App\Http\Controllers\Pos\MetodoPagoController;
use App\Http\Controllers\Pos\ClienteController;
use App\Http\Controllers\Pos\StockLocalController;
use App\Http\Controllers\Pos\VentaFisicaController;
use App\Http\Controllers\Pos\DetalleVentaController;

Route::prefix('pos')->name('pos.')->middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('sedes', SedeController::class)->except(['show']);
    Route::resource('productos', ProductoLocalController::class)->except(['show']);
    Route::resource('lotes', LoteLocalController::class)->except(['show']);
    Route::resource('metodos-pago', MetodoPagoController::class)->except(['show']);
    Route::resource('clientes', ClienteController::class)->except(['show']);

    Route::get('stock', [StockLocalController::class, 'index'])->name('stock.index');
    Route::get('ventas', [VentaFisicaController::class, 'index'])->name('ventas.index');
    Route::get('ventas/{venta}', [VentaFisicaController::class, 'show'])->name('ventas.show');
    Route::get('detalle-ventas', [DetalleVentaController::class, 'index'])->name('detalle-ventas.index');
});
