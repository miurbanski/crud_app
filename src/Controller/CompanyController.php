<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\CompanyService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/api', name: 'api_')]
class CompanyController extends AbstractController
{
    private CompanyService $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    #[Route('/company', name: 'show_all_companies', methods: ['GET'])]
    public function showAll(): Response
    {
        $companies = $this->companyService->getAll();

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
        $this->companyService->create(
            $request->request->get('name'),
            $request->request->get('nip'),
            $request->request->get('address'),
            $request->request->get('city'),
            $request->request->get('postalCode')
        );

        return new JsonResponse('Created new company successfully', Response::HTTP_CREATED);
    }
}
