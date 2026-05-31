<?php

namespace App\Http\Controllers\Companies;

use App\Http\Requests\Company\StoreCompanyRequest;
use App\Services\Company\CompanyService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;


class CompanyController extends Controller
{

    public function __construct(
        private CompanyService $service
    ) {}


    public function store(StoreCompanyRequest $request): JsonResponse
    {

        try {

            $validatedData = $request->validated();

            $result = $this->service->create($validatedData);

            return response()->json($result, 201);

        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 400);
        }

    }



}
