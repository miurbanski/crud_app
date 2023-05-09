<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\Company;
use App\Validator\BaseValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\CompanyService;
use App\Controller\CompanyController;

class CompanyControllerTest extends TestCase
{
    private MockObject $companyServiceMock;
    private MockObject $baseValidatorMock;
    private CompanyController $companyController;

    protected function setUp(): void
    {
        $this->companyServiceMock = $this->createMock(CompanyService::class);
        $this->baseValidatorMock = $this->createMock(BaseValidator::class);
        $this->companyController = new CompanyController($this->companyServiceMock, $this->baseValidatorMock);
    }

    public function testShowAllReturnsJsonResponseWithValidData(): void
    {
        $company1 = new Company();
        $company1->setId(1)
            ->setName('Company 1')
            ->setNip('1234567890')
            ->setAddress('123 Main St')
            ->setCity('Anytown')
            ->setPostalCode('12345');
        $company2 = new Company();
        $company2->setId(2)
            ->setName('Company 2')
            ->setNip('0987654321')
            ->setAddress('456 Elm St')
            ->setCity('Othertown')
            ->setPostalCode('67890');

        $this->companyServiceMock->expects($this->once())
            ->method('getAll')
            ->willReturn([$company1, $company2]);

        $response = $this->companyController->showAll();
        $responseData = json_decode($response->getContent(), true);

        $expectedResponseData = [
            [
                'id' => 1,
                'name' => 'Company 1',
                'nip' => '1234567890',
                'address' => '123 Main St',
                'city' => 'Anytown',
                'postal_code' => '12345',
            ],
            [
                'id' => 2,
                'name' => 'Company 2',
                'nip' => '0987654321',
                'address' => '456 Elm St',
                'city' => 'Othertown',
                'postal_code' => '67890',
            ],
        ];

        $expectedResponse = new JsonResponse($expectedResponseData, Response::HTTP_OK);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertCount(2, $responseData);
        $this->assertEquals($expectedResponse->getContent(), $response->getContent());
    }

    public function testShowReturnsJsonResponseWithValidData(): void
    {
        $company = new Company();
        $company->setId(1)
            ->setName('Company 1')
            ->setNip('1234567890')
            ->setAddress('123 Main St')
            ->setCity('Anytown')
            ->setPostalCode('12345');

        $this->companyServiceMock->expects($this->once())
            ->method('get')
            ->with(1)
            ->willReturn($company);

        $expectedResponseData = [
            [
                'id' => 1,
                'name' => 'Company 1',
                'nip' => '1234567890',
                'address' => '123 Main St',
                'city' => 'Anytown',
                'postal_code' => '12345',
            ]
        ];

        $response = $this->companyController->show(1);
        $responseData = json_decode($response->getContent(), true);

        $expectedResponse = new JsonResponse($expectedResponseData, Response::HTTP_OK);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertCount(1, $responseData);
        $this->assertEquals($expectedResponse->getContent(), $response->getContent());
    }

    public function testCreateReturnsJsonResponseWhenEmployeeIsValid(): void
    {
        $company = new Company();
        $company->setId(1)
            ->setName('Company 1')
            ->setNip('1234567890')
            ->setAddress('123 Main St')
            ->setCity('Anytown')
            ->setPostalCode('12345');

        $request = new Request([], [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john.doe@example.com',
            'telephone_number' => '1234567890',
            'company_id' => 1,
        ]);

        $this->companyServiceMock->expects($this->once())
            ->method('prepare')
            ->willReturn($company);

        $this->baseValidatorMock
            ->expects($this->once())
            ->method('validate')
            ->with($company)
            ->willReturn([]);

        $this->companyServiceMock->expects($this->once())
            ->method('saveEntity')
            ->with($company);

        $response = $this->companyController->create($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals('"Created new company successfully"', $response->getContent());
    }

    public function testUpdateReturnsJsonResponseWhenEmployeeIsValid(): void
    {
        $company = new Company();
        $company->setId(1)
            ->setName('Company 1')
            ->setNip('1234567890')
            ->setAddress('123 Main St')
            ->setCity('Anytown')
            ->setPostalCode('12345');

        $requestContent = json_encode(
            [
                'name' => 'Company 1',
                'nip' => '1234567890',
                'address' => '123 Main St',
                'city' => 'Anytown',
                'postal_code' => '12345',
            ]
        );

        $request = new Request([], [], [], [], [], [], $requestContent);

        $this->companyServiceMock
            ->expects($this->once())
            ->method('get')
            ->with(1)
            ->willReturn($company);

        $this->companyServiceMock
            ->expects($this->once())
            ->method('prepare')
            ->with(
                $company,
                'Company 1',
                '1234567890',
                '123 Main St',
                'Anytown',
                '12345'
            )->willReturn($company);

        $this->baseValidatorMock
            ->expects($this->once())
            ->method('validate')
            ->with($company)
            ->willReturn([]);

        $this->companyServiceMock
            ->expects($this->once())
            ->method('saveEntity')
            ->with($company);

        $response = $this->companyController->update(1, $request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('"Updated company successfully"', $response->getContent());
    }

    public function testDeleteReturnsNoContentWhenCompanyIsDeleted(): void
    {
        $companyId = 123;
        $companyMock = $this->createMock(Company::class);

        $this->companyServiceMock
            ->expects($this->once())
            ->method('get')
            ->with($companyId)
            ->willReturn($companyMock);

        $this->companyServiceMock
            ->expects($this->once())
            ->method('delete')
            ->with($companyMock);

        $response = $this->companyController->delete($companyId);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(JsonResponse::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}