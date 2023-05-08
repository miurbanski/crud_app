<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Employee;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    private EntityManager $entityManager;
    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(Employee::class);
    }

    public function save(Employee $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function get(int $id): ?Employee
    {
        return $this->repository->findOneBy(['id' => $id]);
    }

    public function getAll(): array
    {
        return $this->repository->findAll();
    }

    public function delete(Employee $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }
}
