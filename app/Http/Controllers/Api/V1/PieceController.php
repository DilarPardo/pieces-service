<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Piece;
use Illuminate\Http\Request;
use App\Http\Requests\StorePieceRequest;
use App\Http\Requests\UpdatePieceRequest;
use Illuminate\Http\JsonResponse;

class PieceController extends Controller
{
    
    public function index(Request $request): JsonResponse
    {

        $query = Piece::query();

        if ($request->has('block_id')) {
            $query->where('block_id', $request->block_id);
        }

        return response()->json($query->with('block:id,name')->paginate(15));

    }

    public function store(StorePieceRequest $request): JsonResponse
    {

        $validated = $request->validated();
        $validated['created_by'] = $request->authenticated_user['id'];

        $piece = Piece::create($validated);
        return response()->json($piece, 201);

    }

    public function show(Piece $piece)
    {
        
        return response()->json($piece->load('fabrications'), 200);

    }

    public function update(UpdatePieceRequest $request, Piece $piece): JsonResponse
    {

        $piece->update($request->validated());
        return response()->json($piece);
        
    }

    public function destroy(Piece $piece)
    {

        $piece->delete();
        return response()->json(['message' => 'Pieza eliminada'], 200);

    }
    
}
