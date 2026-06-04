<?php

namespace App\Http\Controllers\Companies;

use App\Http\Requests\Company\StoreClientFromCompanyRequest;
use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Models\Company;
use App\Services\Company\CompanyService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;


class CompanyController extends Controller
{

    public function __construct(
        private CompanyService $service
    ) {}

    public function show(Company $company): JsonResponse
    {
        return response()->json($company);

    }


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

    public function update(UpdateCompanyRequest $request, Company $company): JsonResponse
    {

        try {

            $validatedData = $request->validated();

            if (empty($validatedData)) {
                return response()->json('No fields to update', 422);
            }

            $result = $this->service->update($company, $validatedData);

            return response()->json($result);
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 500);
        }

    }

    public function storeClient(StoreClientFromCompanyRequest $request, int $company_id): JsonResponse
    {
        try {

            $validatedData = $request->validated();

            $result = $this->service->storeClientFromCompany($company_id, $validatedData);

            return response()->json($result, 201);

        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 500);
        }

    }

    public function client($company_id): JsonResponse
    {

        try {

            $contacts = $this->service->getClient($company_id);

            return response()->json($contacts);

        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 500);
        }

    }



}
