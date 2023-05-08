<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;

class CompanyRepository implements CompanyRepositoryInterface
{
    private EntityManager $entityManager;
    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(Company::class);
    }

    public function save(Company $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function update(Company $entity): void
    {

    }

    public function delete(Company $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    public function get(int $id): ?Company
    {
        return $this->repository->findOneBy(['id' => $id]);
    }

    public function getAll(): array
    {
        return $this->repository->findAll();
    }
}
