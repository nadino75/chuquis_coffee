<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

trait ApiResourceTrait
{
    protected string $modelClass;
    protected string $primaryKey = 'id';
    protected array $createRules = [];
    protected array $updateRules = [];

    public function indexApi(Request $request): JsonResponse
    {
        $query = $this->modelClass::query();

        if (method_exists($this, 'indexQuery')) {
            $query = $this->indexQuery($query, $request);
        }

        $perPage = $request->get('per_page', 10);
        $items = $query->paginate($perPage);

        return response()->json($items);
    }

    public function showApi($id): JsonResponse
    {
        $item = $this->modelClass::where($this->primaryKey, $id)->firstOrFail();
        return response()->json($item);
    }

    public function storeApi(Request $request): JsonResponse
    {
        $rules = method_exists($this, 'getStoreRules') ? $this->getStoreRules() : $this->createRules;
        if ($rules) {
            $validated = $request->validate($rules);
        } else {
            $validated = $request->all();
        }

        $item = $this->modelClass::create($validated);
        return response()->json($item, 201);
    }

    public function updateApi(Request $request, $id): JsonResponse
    {
        $item = $this->modelClass::where($this->primaryKey, $id)->firstOrFail();

        $rules = method_exists($this, 'getUpdateRules') ? $this->getUpdateRules($id) : $this->updateRules;
        if ($rules) {
            $validated = $request->validate($rules);
        } else {
            $validated = $request->all();
        }

        $item->update($validated);
        return response()->json($item);
    }

    public function destroyApi($id): JsonResponse
    {
        $item = $this->modelClass::where($this->primaryKey, $id)->firstOrFail();
        $item->delete();
        return response()->json(['message' => 'Registro eliminado exitosamente']);
    }
}
