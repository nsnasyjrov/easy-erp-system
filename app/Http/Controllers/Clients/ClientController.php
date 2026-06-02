<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\DestroyClientRequest;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Services\Client\ClientService;
use Illuminate\Http\JsonResponse;


class ClientController extends Controller
{

    public function __construct(
        private ClientService $service
    ) {}


    public function index()
    {
        try {
            $clients = $this->service->index();

            return response()->json($clients);

        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 500);
        }
    }

    public function show(int $client_id)
    {
        try {

            $client = $this->service->show($client_id);

            return response()->json($client);

        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 500);
        }
    }


    public function store(StoreClientRequest $request): JsonResponse
    {
        try{

            $validatedData = $request->validated();
            $result = $this->service->create($validatedData);

            return response()->json($result, 201);

        } catch (\Exception $exception) {
            // На данном этапе пускай везде код 500 - в будущем нужно обработать другие exception-сценарии.
            return response()->json($exception->getMessage(), 500);
        }
    }

    public function update(UpdateClientRequest $request): JsonResponse
    {

        try {
            $validatedData = $request->validated();
            $fields = array_diff(array_keys($validatedData), ['id']);

            if (empty($fields)) {
                return response()->json(['message' => 'Нет полей для обновления'], 422);
            }

            $result = $this->service->update($validatedData);

            return response()->json($result);

        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 500);
        }
    }

    public function destroy(DestroyClientRequest $request): JsonResponse
    {

        try {

            $validatedData = $request->validated();

            $result = $this->service->delete($validatedData);

            if (empty($result)) {
                return response()->json(['message' => 'Client not found'], 404);
            }

            return response()->json($result, 204);

        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 500);
        }

    }
}
