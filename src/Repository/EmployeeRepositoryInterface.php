<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Employee;

interface EmployeeRepositoryInterface
{
    public function save(Employee $entity): void;

    public function get(int $id): ?Employee;

    public function getAll(): array;

    public function delete(Employee $entity): void;
}