<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Piece;
use App\Models\PieceFabrication;

class ReportController extends Controller
{

    public function getDashboardStats()
    {
        // Totales generales para tarjetas informativas
        $totalDefined = Piece::count();
        $totalFabricated = PieceFabrication::count();
        
        // Reporte agrupado por proyecto
        $projectsReport = Project::withCount([
            'blocks as total_pieces' => function ($query) {
                $query->join('pieces', 'blocks.id', '=', 'pieces.block_id');
            }
        ])->get()->map(function($project) {
            // Calculamos manualmente las fabricadas por proyecto para mayor precisión
            $fabricatedInProject = PieceFabrication::whereHas('piece.block', function($q) use ($project) {
                $q->where('project_id', $project->id);
            })->count();

            return [
                'project_name' => $project->name,
                'total_pieces' => $project->total_pieces,
                'fabricated' => $fabricatedInProject,
                'pending' => $project->total_pieces - $fabricatedInProject,
                'completion_percentage' => $project->total_pieces > 0 
                    ? round(($fabricatedInProject / $project->total_pieces) * 100, 2) 
                    : 0
            ];
        });

        return response()->json([
            'summary' => [
                'total_defined' => $totalDefined,
                'total_fabricated' => $totalFabricated,
                'total_pending' => $totalDefined - $totalFabricated,
            ],
            'projects_detail' => $projectsReport
        ], 200);
    }

}
