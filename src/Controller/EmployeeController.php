<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Employee;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\EmployeeService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Validator\BaseValidator;

#[Route('/api', name: 'api_')]
class EmployeeController extends AbstractController
{
    private EmployeeService $employeeService;
    private BaseValidator $validator;

    public function __construct(EmployeeService $employeeService, BaseValidator $validator)
    {
        $this->employeeService = $employeeService;
        $this->validator = $validator;
    }

    #[Route('/employee', name: 'show_all_employees', methods: ['GET'])]
    public function showAll(): Response
    {
        $results = $this->employeeService->getAll();

        foreach ($results as $result) {
            $data[] = [
                'id' => $result->getId(),
                'firstname' => $result->getFirstname(),
                'lastname' => $result->getLastname(),
                'email' => $result->getEmail(),
                'telephone_number' => $result->getTelephoneNumber(),
                'company_id' => $result->getCompany()->getId()
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/employee/{id}', name: 'show_employee', methods: ['GET'])]
    public function show(int $id): Response
    {
        $employee = $this->employeeService->get($id);

        if (!$employee) {
            throw new NotFoundHttpException('Not found employee with id: ' . $id);
        }

        $data[] = [
            'id' => $employee->getId(),
            'firstname' => $employee->getFirstname(),
            'lastname' => $employee->getLastname(),
            'email' => $employee->getEmail(),
            'telephone_number' => $employee->getTelephoneNumber(),
            'company_id' => $employee->getCompany()->getId()
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/employee', name: 'create_employee', methods: ['POST'])]
    public function create(Request $request): Response
    {
       $employee = $this->employeeService->prepare(
            new Employee(),
            $request->request->get('firstname') ?? '',
            $request->request->get('lastname') ?? '',
            $request->request->get('email') ?? '',
            $request->request->get('telephone_number') ?? '',
            (int)$request->request->get('company_id')
        );

        $validation = $this->validator->validate($employee);
        
        //dd($validation);

        if (!empty($validation['validation_failed'])) {
            return new JsonResponse($validation, Response::HTTP_BAD_REQUEST);
        }
        $this->employeeService->saveEntity($employee);

        return new JsonResponse('Created new employee successfully', Response::HTTP_CREATED);
    }

    #[Route('/employee/{id}', name: 'update_employee', methods: ['PUT'])]
    public function update(int $id, Request $request): Response
    {
        $employee = $this->employeeService->get($id);

        if (!$employee) {
            throw new NotFoundHttpException('Not found employee with id: ' . $id);
        }

        $data = json_decode($request->getContent(), true);
        
        $employee = $this->employeeService->prepare(
            $employee,
            $data['firstname'] ?? '',
            $data['lastname'] ?? '',
            $data['email'] ?? '',
            $data['telephone_number'] ?? '',
            $data['company_id'] ?? ''
        );

        $validation = $this->validator->validate($employee);

        if (!empty($validation['validation_failed'])) {
            return new JsonResponse($validation, Response::HTTP_BAD_REQUEST);
        }
        $this->employeeService->saveEntity($employee);

        return new JsonResponse('Updated employee successfully', Response::HTTP_OK);
    }

    #[Route('/employee/{id}', name: 'delete_employee', methods: ['DELETE'])]
    public function delete(int $id, Request $request): Response
    {
        $employee = $this->employeeService->get($id);

        if (!$employee) {
            throw new NotFoundHttpException('Not found employee with id: ' . $id);
        }
        $this->employeeService->delete($employee);

        return new JsonResponse('', Response::HTTP_NO_CONTENT);
    }
}