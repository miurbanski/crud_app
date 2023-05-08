<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\CompanyRepository;
use App\Entity\Company;

class CompanyService
{
    private CompanyRepository $companyRepository;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    public function prepare(
        Company $company,
        string $name,
        string $nip,
        string $address,
        string $city,
        string $postalCode
    ): Company {
        $company->setName($name);
        $company->setNip($nip);
        $company->setAddress($address);
        $company->setCity($city);
        $company->setPostalCode($postalCode);

        return $company;
    }

    public function saveEntity(
        Company $company
    ): void {
        try {
            $this->companyRepository->save($company);
        } catch(\Exception $e) {
            throw $e;
        }
    }

    public function getAll(): array
    {
        return $this->companyRepository->getAll();  
    }

    public function get(int $id): ?Company
    {
        return $this->companyRepository->get($id);
    }

    public function delete(Company $entity): void
    {
        $this->companyRepository->delete($entity);
    }
}
