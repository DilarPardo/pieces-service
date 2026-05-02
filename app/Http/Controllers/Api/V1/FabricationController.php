<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PieceFabrication;
use Illuminate\Http\Request;
use App\Models\Piece;
use App\Http\Requests\StoreFabricationRequest; 

class FabricationController extends Controller
{
    
    public function index()
    {
        
        return response()->json(PieceFabrication::with('piece')->latest()->get(), 200);

    }

    public function store(StoreFabricationRequest $request)
    {

        $data = $request->validated();

        $piece = Piece::findOrFail($data['piece_id']);
        
        $data['weight_diff'] = $data['real_weight'] - $piece->theoretical_weight;

        $data['created_by'] = 1;

        $fabrication = PieceFabrication::create($data);

        return response()->json([
            'message' => 'Registro de fabricación creado con éxito',
            'data'    => $fabrication->load('piece')
        ], 201);

    }

    public function getDashboardStats(): JsonResponse
    {
        
        $projectsReport = Project::withCount([
            
            'blocks as total_pieces' => function ($query) {
                $query->join('pieces', 'blocks.id', '=', 'pieces.block_id');
            },
           
            'blocks as fabricated_count' => function ($query) {
                $query->join('pieces', 'blocks.id', '=', 'pieces.block_id')
                    ->join('piece_fabrications', 'pieces.id', '=', 'piece_fabrications.piece_id');
            }
        ])->get()->map(function($project) {
            return [
                'project_name' => $project->name,
                'total_pieces' => $project->total_pieces ?? 0,
                'fabricated'   => $project->fabricated_count ?? 0,
                'pending'      => ($project->total_pieces ?? 0) - ($project->fabricated_count ?? 0),
                'completion_percentage' => $project->total_pieces > 0 
                    ? round(($project->fabricated_count / $project->total_pieces) * 100, 2) 
                    : 0
            ];
        });

    }
   
    public function show(PieceFabrication $fabrication)
    {

        return response()->json($fabrication->load('piece.block.project'), 200);

    }
    
    public function update(Request $request, PieceFabrication $fabrication)
    {

        $validated = $request->validate([
            'real_weight' => 'sometimes|numeric',
            'observations' => 'nullable|string',
            'status' => 'sometimes|in:Pendiente,Fabricada'
        ]);

        $fabrication->update($validated);
        
        return response()->json([
            'message' => 'Registro de fabricación actualizado',
            'data' => $fabrication
        ], 200);

    }
    
    public function destroy(PieceFabrication $fabrication)
    {

        $fabrication->delete();
        return response()->json(['message' => 'Registro de fabricación eliminado'], 200);

    }

}
