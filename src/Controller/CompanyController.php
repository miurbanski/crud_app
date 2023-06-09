<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Company;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\CompanyService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Validator\BaseValidator;

#[Route('/api', name: 'api_')]
class CompanyController extends AbstractController
{
    private CompanyService $companyService;
    private BaseValidator $validator;

    public function __construct(CompanyService $companyService, BaseValidator $validator)
    {
        $this->companyService = $companyService;
        $this->validator = $validator;
    }

    #[Route('/company', name: 'show_all_companies', methods: ['GET'])]
    public function showAll(): Response
    {
        $companies = $this->companyService->getAll();

        $data = [];

        foreach ($companies as $company) {
            $data[] = [
                'id' => $company->getId(),
                'name' => $company->getName(),
                'nip' => $company->getNip(),
                'address' => $company->getAddress(),
                'city' => $company->getCity(),
                'postal_code' => $company->getPostalCode()
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/company/{id}', name: 'show_company', methods: ['GET'])]
    public function show(int $id): Response
    {
        $company = $this->companyService->get($id);

        if (!$company) {
            throw new NotFoundHttpException('Not found company with id: ' . $id);
        }

        $data[] = [
            'id' => $company->getId(),
            'name' => $company->getName(),
            'nip' => $company->getNip(),
            'address' => $company->getAddress(),
            'city' => $company->getCity(),
            'postal_code' => $company->getPostalCode()
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/company', name: 'create_company', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $company = $this->companyService->prepare(
            new Company(),
            $request->request->get('name') ?? '',
            $request->request->get('nip') ?? '',
            $request->request->get('address') ?? '',
            $request->request->get('city') ?? '',
            $request->request->get('postalCode') ?? ''
        );

        $validation = $this->validator->validate($company);

        if (!empty($validation['validation_failed'])) {
            return new JsonResponse($validation, Response::HTTP_BAD_REQUEST);
        }
        $this->companyService->saveEntity($company);

        return new JsonResponse('Created new company successfully', Response::HTTP_CREATED);
    }

    #[Route('/company/{id}', name: 'update_company', methods: ['PUT'])]
    public function update(int $id, Request $request): Response
    {
        $company = $this->companyService->get($id);

        if (!$company) {
            throw new NotFoundHttpException('Not found employee with id: ' . $id);
        }

        $data = json_decode($request->getContent(), true);

        $company = $this->companyService->prepare(
            $company,
            $data['name'] ?? '',
            $data['nip'] ?? '',
            $data['address'] ?? '',
            $data['city'] ?? '',
            $data['postal_code'] ?? ''
        );

        $validation = $this->validator->validate($company);

        if (!empty($validation['validation_failed'])) {
            return new JsonResponse($validation, Response::HTTP_BAD_REQUEST);
        }
        $this->companyService->saveEntity($company);

        return new JsonResponse('Updated company successfully', Response::HTTP_OK);
    }

    #[Route('/company/{id}', name: 'delete_company', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $company = $this->companyService->get($id);

        if (!$company) {
            throw new NotFoundHttpException('Not found company with id: ' . $id);
        }
        $this->companyService->delete($company);

        return new JsonResponse('', Response::HTTP_NO_CONTENT);
    }
}
