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

    public function create(
        string $name,
        string $nip,
        string $address,
        string $city,
        string $postalCode
    ): void {
        $company = new Company();
        $company->setName($name);
        $company->setNip($nip);
        $company->setAddress($address);
        $company->setCity($city);
        $company->setPostalCode($postalCode);

        $this->companyRepository->save($company);
    }

    public function getAll(): array
    {
        return $this->companyRepository->getAll();  
    }

    public function get(int $id): ?Company
    {
        return $this->companyRepository->get($id);
    }
}
