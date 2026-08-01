<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\EnsureClientContactsRequest;
use App\Http\Requests\Client\IndexClientRequest;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ContactInfoResource;
use App\Models\Client;
use App\Services\Client\ClientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ClientController extends Controller
{

    public function __construct(
        private ClientService $service
    ) {}

    public function index(IndexClientRequest $request): AnonymousResourceCollection
    {
        $clients = $this->service->getPaginatedList($request->validated());

        return (ClientResource::collection($clients));
    }

    public function show(Client $client): ClientResource
    {
            return (new  ClientResource($client));
    }

    public function store(StoreClientRequest $request): JsonResponse
    {

            $validatedData = $request->validated();
            $result = $this->service->create($validatedData);

            return (new ClientResource($result))
                    ->additional(["message" => "Client created"])
                    ->response()->setStatusCode(201);
    }

    public function update(UpdateClientRequest $request, Client $client): ClientResource|JsonResponse
    {

            $validatedData = $request->validated();

            $result = $this->service->update( $client, $validatedData);

            return (new ClientResource($result))->additional(["message" => "Client updated"]);
    }

    public function destroy(Client $client): Response
    {
        $this->service->delete($client);

        return response()->noContent();
    }

    public function ensureClientContacts(EnsureClientContactsRequest $request, Client $client): JsonResponse
    {

            $validatedData = $request->validated();

            $result = $this->service->ensureClientContacts($validatedData, $client);

            return (new ContactInfoResource($result))
                    ->additional(["message" => "Added client contacts"])
                    ->response()->setStatusCode(201);
    }

    public function contacts(Client $client): AnonymousResourceCollection
    {

        $contacts = $client->contacts()->get();

            return (ContactInfoResource::collection($contacts));
    }

}
