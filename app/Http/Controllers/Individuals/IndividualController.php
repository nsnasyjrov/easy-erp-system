<?php

namespace App\Http\Controllers\Individuals;

use App\Http\Controllers\Controller;
use App\Http\Requests\Individual\StoreIndividualRequest;
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


}
