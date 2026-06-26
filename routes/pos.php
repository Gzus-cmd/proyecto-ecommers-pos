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

Route::middleware(['auth'])->prefix('pos')->name('pos.')->group(function () {
    // Dashboard y Ventas — ambos roles pueden ver
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('ventas', [VentaFisicaController::class, 'index'])->name('ventas.index');
    Route::get('ventas/create', [VentaFisicaController::class, 'create'])->name('ventas.create');
    Route::post('ventas', [VentaFisicaController::class, 'store'])->name('ventas.store');
    Route::get('ventas/{venta}', [VentaFisicaController::class, 'show'])->name('ventas.show');
    Route::get('stock', [StockLocalController::class, 'index'])->name('stock.index');
    Route::get('detalle-ventas', [DetalleVentaController::class, 'index'])->name('detalle-ventas.index');

    // Productos — ambos pueden VER, solo admin puede crear/editar/eliminar
    Route::get('productos', [ProductoLocalController::class, 'index'])->name('productos.index');
    Route::get('productos/{producto}', [ProductoLocalController::class, 'show'])->name('productos.show');
    Route::middleware('role:admin')->group(function () {
        Route::get('productos/create', [ProductoLocalController::class, 'create'])->name('productos.create');
        Route::post('productos', [ProductoLocalController::class, 'store'])->name('productos.store');
        Route::get('productos/{producto}/edit', [ProductoLocalController::class, 'edit'])->name('productos.edit');
        Route::put('productos/{producto}', [ProductoLocalController::class, 'update'])->name('productos.update');
        Route::delete('productos/{producto}', [ProductoLocalController::class, 'destroy'])->name('productos.destroy');
    });

    // Lotes — solo admin puede modificar
    Route::get('lotes', [LoteLocalController::class, 'index'])->name('lotes.index');
    Route::get('lotes/{lote}', [LoteLocalController::class, 'show'])->name('lotes.show');
    Route::middleware('role:admin')->group(function () {
        Route::get('lotes/create', [LoteLocalController::class, 'create'])->name('lotes.create');
        Route::post('lotes', [LoteLocalController::class, 'store'])->name('lotes.store');
        Route::get('lotes/{lote}/edit', [LoteLocalController::class, 'edit'])->name('lotes.edit');
        Route::put('lotes/{lote}', [LoteLocalController::class, 'update'])->name('lotes.update');
        Route::delete('lotes/{lote}', [LoteLocalController::class, 'destroy'])->name('lotes.destroy');
        Route::post('stock/{lote}/retirar', [LoteLocalController::class, 'retirar'])->name('stock.retirar');
    });

    // Clientes — solo admin puede modificar
    Route::get('clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('clientes/{cliente}', [ClienteController::class, 'show'])->name('clientes.show');
    Route::middleware('role:admin')->group(function () {
        Route::get('clientes/create', [ClienteController::class, 'create'])->name('clientes.create');
        Route::post('clientes', [ClienteController::class, 'store'])->name('clientes.store');
        Route::get('clientes/{cliente}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
        Route::put('clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
        Route::delete('clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
        Route::get('clientes/search-by-dni', [ClienteController::class, 'searchByDni'])->name('clientes.search-by-dni');
    });

    // Métodos de Pago — solo admin
    Route::middleware('role:admin')->group(function () {
        Route::get('metodos-pago', [MetodoPagoController::class, 'index'])->name('metodos-pago.index');
        Route::get('metodos-pago/create', [MetodoPagoController::class, 'create'])->name('metodos-pago.create');
        Route::post('metodos-pago', [MetodoPagoController::class, 'store'])->name('metodos-pago.store');
        Route::get('metodos-pago/{metodoPago}/edit', [MetodoPagoController::class, 'edit'])->name('metodos-pago.edit');
        Route::put('metodos-pago/{metodoPago}', [MetodoPagoController::class, 'update'])->name('metodos-pago.update');
        Route::delete('metodos-pago/{metodoPago}', [MetodoPagoController::class, 'destroy'])->name('metodos-pago.destroy');
    });

    // Usuarios — solo admin
    Route::middleware('role:admin')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});
