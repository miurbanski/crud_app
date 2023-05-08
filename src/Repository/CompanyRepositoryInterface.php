<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Company;

interface CompanyRepositoryInterface
{
    public function save(Company $entity): void;

    public function get(int $id): ?Company;

    public function getAll(): array;

    public function delete(Company $entity): void;
}