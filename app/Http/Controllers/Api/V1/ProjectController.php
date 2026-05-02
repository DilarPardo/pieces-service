<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    
    public function index(Request $request): JsonResponse
    {

        $query = Project::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->withCount('blocks')->paginate(10));

    }

    public function store(StoreProjectRequest $request): JsonResponse
    {

        $validated = $request->validated();
        
        $validated['created_by'] = $request->authenticated_user['id'];

        $project = Project::create($validated);
        return response()->json($project, 201);

    }

    public function show(Project $project)
    {

        return response()->json($project->load('blocks.pieces'), 200);

    }

    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {

        $project->update($request->validated());
        return response()->json($project);
        
    }

    public function destroy(Project $project)
    {

        $project->delete();
        return response()->json(['message' => 'Proyecto eliminado (Soft Delete)'], 200);

    }

}
