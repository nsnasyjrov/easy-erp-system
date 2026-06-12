<?php

namespace App\Http\Controllers\Individuals;

use App\Http\Controllers\Controller;
use App\Http\Requests\Individual\EnsureClientFromIndividualRequest;
use App\Http\Requests\Individual\StoreIndividualRequest;
use App\Http\Requests\Individual\UpdateIndividualRequest;
use App\Http\Resources\ClientResource;
use App\Http\Resources\IndividualResource;
use App\Models\Individual;
use App\Services\Individual\IndividualService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndividualController extends Controller
{

    public function __construct(
        private IndividualService $service
    ) {}

    public function index(): AnonymousResourceCollection
    {

        $individuals = Individual::latest()->paginate(20);

        return (IndividualResource::collection($individuals));
    }

    public function show(Individual $individual): IndividualResource
    {

        return new IndividualResource($individual);

    }

    public function store(StoreIndividualRequest $request): JsonResponse
    {

        $validatedData = $request->validated();

        $individual = $this->service->createIndividual($validatedData);

        return (new IndividualResource($individual))
            ->additional(["message" => "Individual created successfully"])
            ->response()->setStatusCode(201);
    }

    public function update(UpdateIndividualRequest $request, Individual $individual) : IndividualResource
    {

        $validatedData = $request->validated();

        if (empty($validatedData)) abort(422, "No fields to update");

        $individual = $this->service->updateIndividual($individual, $validatedData);

        return new IndividualResource($individual);
    }

    public function destroy(Individual $individual)
    {

        $this->service->deleteIndividual($individual);
        return response()->noContent();

    }

    public function client(Individual $individual): ClientResource
    {
        $client = $individual->client;

        if (empty($client)) abort(404, "The client does not exist for this individual");

        return new ClientResource($client->load('contacts'));

    }

    public function ensureClient(Individual $individual, EnsureClientFromIndividualRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $client = $this->service->storeClientFromIndividual($individual, $validatedData);

        return (new ClientResource($client))
               ->additional(["message" => "Client ensured successfully"])
               ->response()->setStatusCode(201);

    }

}
