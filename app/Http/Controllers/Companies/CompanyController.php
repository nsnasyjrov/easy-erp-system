<?php

namespace App\Http\Controllers\Companies;

use App\Http\Requests\Company\StoreClientFromCompanyRequest;
use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Http\Resources\ClientResource;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Services\Company\CompanyService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;


class CompanyController extends Controller
{

    public function __construct(
        private CompanyService $service
    ) {}

    public function index()
    {

        $companies = Company::all();

        return CompanyResource::collection($companies);
    }

    public function show(Company $company): CompanyResource
    {

        return new CompanyResource($company);

    }


    public function store(StoreCompanyRequest $request): JsonResponse
    {

         $validatedData = $request->validated();

            $result = $this->service->create($validatedData);

            return response()->json($result, 201);
    }

    public function update(UpdateCompanyRequest $request, Company $company): JsonResponse
    {


            $validatedData = $request->validated();

            if (empty($validatedData)) {
                return response()->json('No fields to update', 422);
            }

            $result = $this->service->update($company, $validatedData);

            return response()->json($result);

    }

    public function storeClient(StoreClientFromCompanyRequest $request, Company $company): JsonResponse
    {

            $validatedData = $request->validated();

            $result = $this->service->storeClientFromCompany($company, $validatedData);

            return response()->json($result, 201);
    }

    public function client(Company $company): ClientResource
    {

         $client = $company->client;

         return new ClientResource($client);

    }

    public function destroy(Company $company)
    {
        $result = $company->delete();

        if (empty($result)) abort(404, "Company not found");

        return response()->noContent();

    }



}
