<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\EmployeeRepository;
use App\Entity\Employee;
use App\Repository\CompanyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Exception;

class EmployeeService
{
    private EmployeeRepository $employeeRepository;
    private CompanyRepository $companyRepository;

    public function __construct(
        EmployeeRepository $employeeRepository,
        CompanyRepository $companyRepository
    ) {
        $this->employeeRepository = $employeeRepository;
        $this->companyRepository = $companyRepository;
    }

    public function prepare(
        Employee $employee,
        string $firstname,
        string $lastname,
        string $email,
        string $telephoneNumber,
        ?int $companyId
    ): Employee {
        $employee->setFirstname($firstname);
        $employee->setLastname($lastname);
        $employee->setEmail($email);

        if (!empty($telephoneNumber)) {
            $employee->setTelephoneNumber($telephoneNumber);
        }

        if (!empty($companyId)) {
            $company = $this->companyRepository->get($companyId);

            if (!$company) {
                throw new NotFoundHttpException('Not found company');
            }
            $employee->setCompany($company);
        }
        return $employee;
    }

    public function saveEntity(
        Employee $employee
    ): void {
        try {
            $this->employeeRepository->save($employee);
        } catch(\Exception $e) {
            throw $e;
        }
    }

    public function getAll(): array
    {
        return $this->employeeRepository->getAll();  
    }

    public function get(int $id): ?Employee
    {
        return $this->employeeRepository->get($id);
    }
    
    public function delete(Employee $entity): void
    {
        $this->employeeRepository->delete($entity);
    }
}
