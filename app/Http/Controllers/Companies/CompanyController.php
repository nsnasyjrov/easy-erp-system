<?php

namespace App\Http\Controllers\Companies;

use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Services\Company\CompanyService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use mysql_xdevapi\Exception;


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

    public function update(UpdateCompanyRequest $request)
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



}
