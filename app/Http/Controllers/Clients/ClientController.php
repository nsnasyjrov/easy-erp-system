<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreClientRequest;
use App\Services\Client\ClientService;
use \Illuminate\Http\JsonResponse;


class ClientController extends Controller
{

    public function store(StoreClientRequest $request, ClientService $service): JsonResponse
    {
        try{

            $validatedData = $request->validated();
            $result = $service->create($validatedData);

            return response()->json($result, 201);

        } catch (\Exception $exception) {
            // На данном этапе пускай везде код 500 - в будущем нужно обработать другие exception-сценарии.
            return response()->json($exception->getMessage(), 500);
        }
    }


}
