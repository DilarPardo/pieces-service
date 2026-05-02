<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\BlockController;
use App\Http\Controllers\Api\V1\PieceController;
use App\Http\Controllers\Api\V1\FabricationController;


Route::prefix('v1')->middleware('auth.remote')->group(function () {
    
    // Rutas para Proyectos
    Route::apiResource('projects', ProjectController::class);
    
    // Rutas para Bloques
    Route::apiResource('blocks', BlockController::class);
    
    // Rutas para Piezas
    Route::apiResource('pieces', PieceController::class);
    
    // Rutas para Fabricación
    Route::apiResource('fabrications', FabricationController::class);

    // Rutas para Reportes
    Route::get('reports/dashboard', [ReportController::class, 'getDashboardStats']);

});
