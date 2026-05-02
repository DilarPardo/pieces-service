<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Block;
use Illuminate\Http\Request;
use App\Http\Requests\StoreBlockRequest;
use App\Http\Requests\UpdateBlockRequest;
use Illuminate\Http\JsonResponse;

class BlockController extends Controller
{
    
    public function index(Request $request): JsonResponse
    {

        $query = Block::query();

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        return response()->json($query->with('project:id,name,code')->paginate(10));

    }
  
    public function store(StoreBlockRequest $request): JsonResponse
    {

        $validated = $request->validated();
        $validated['created_by'] = $request->authenticated_user['id'];

        $block = Block::create($validated);
        return response()->json($block, 201);
        
    }

    public function show(Block $block)
    {

        return response()->json($block->load('pieces'), 200);

    }

    public function update(UpdateBlockRequest $request, Block $block): JsonResponse
    {

        $block->update($request->validated());
        return response()->json($block);

    }

    public function destroy(Block $block)
    {

        $block->delete();
        return response()->json(['message' => 'Bloque eliminado correctamente'], 200);

    }
    
}
