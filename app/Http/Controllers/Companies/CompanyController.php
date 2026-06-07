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
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;


class   CompanyController extends Controller
{

    public function __construct(
        private CompanyService $service
    ) {}

    public function index(): AnonymousResourceCollection
    {

        $companies = Company::latest()->paginate(20);

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

            return (new CompanyResource($result))
                   ->additional(["message" => "Company created"])
                   ->response()->setStatusCode(201);
    }

    public function update(UpdateCompanyRequest $request, Company $company): CompanyResource|JsonResponse
    {
            $validatedData = $request->validated();

            if (empty($validatedData)) {
                return response()->json(["message" => "Not fields to update"], 422);
            }

            $result = $this->service->update($company, $validatedData);

            return (new CompanyResource($result))
                   ->additional(["message" => "Company updated"]);

    }

    public function storeClient(StoreClientFromCompanyRequest $request, Company $company): JsonResponse
    {

            $validatedData = $request->validated();

            $result = $this->service->storeClientFromCompany($company, $validatedData);

            return (new ClientResource($result))
                    ->additional(["message" => "Client created"])
                    ->response()->setStatusCode(201);
    }

    public function client(Company $company): ClientResource|JsonResponse
    {

         $client = $company->client;

         if (empty($client)) {
             return response()->json(["message" => "Company is not linked to Client"], 404);
         }

         return new ClientResource($client);

    }

    public function destroy(Company $company): Response
    {

        if ($company->client()->exists()) abort(409, 'Company is linked to client and cannot be deleted');

        $company->delete();

        return response()->noContent();

    }
}
