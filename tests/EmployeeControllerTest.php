<?php

declare(strict_types=1);

namespace App\Tests;

use App\Controller\EmployeeController;
use App\Entity\Employee;
use App\Entity\Company;
use App\Service\EmployeeService;
use App\Validator\BaseValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;

class EmployeeControllerTest extends TestCase
{
    private MockObject $employeeServiceMock;
    private MockObject $baseValidatorMock;
    private EmployeeController $employeeControllerMock;

    protected function setUp(): void
    {
        $this->employeeServiceMock = $this->createMock(EmployeeService::class);
        $this->baseValidatorMock = $this->createMock(BaseValidator::class);
        $this->employeeControllerMock = new EmployeeController($this->employeeServiceMock, $this->baseValidatorMock);
    }

    public function testShowAllReturnsJsonResponse(): void
    {
        $employee1 = new Employee();
        $employee1->setId(1);
        $employee1->setFirstname('John');
        $employee1->setLastname('Doe');
        $employee1->setEmail('johndoe@example.com');
        $employee1->setTelephoneNumber('123-456-7890');

        $employee2 = new Employee();
        $employee2->setId(2);
        $employee2->setFirstname('Jane');
        $employee2->setLastname('Doe');
        $employee2->setEmail('janedoe@example.com');
        $employee2->setTelephoneNumber('987-654-3210');

        $company = new Company();
        $company->setId(1);
        $company->setName('Acme Inc.');

        $employee1->setCompany($company);
        $employee2->setCompany($company);

        $employees = [$employee1, $employee2];

        $this->employeeServiceMock->expects($this->once())
            ->method('getAll')
            ->willReturn($employees);

        $expectedResponseData = [
            [
                'id' => 1,
                'firstname' => 'John',
                'lastname' => 'Doe',
                'email' => 'johndoe@example.com',
                'telephone_number' => '123-456-7890',
                'company_id' => 1,
            ],
            [
                'id' => 2,
                'firstname' => 'Jane',
                'lastname' => 'Doe',
                'email' => 'janedoe@example.com',
                'telephone_number' => '987-654-3210',
                'company_id' => 1,
            ],
        ];

        $expectedResponse = new JsonResponse($expectedResponseData, Response::HTTP_OK);

        $response = $this->employeeControllerMock->showAll();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($expectedResponse->getContent(), $response->getContent());
        $this->assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    public function testShowReturnsJsonResponseWithEmployeeData(): void
    {
        $company = new Company();
        $company->setId(1);
        $company->setName('Acme Inc.');

        $employee = new Employee();
        $employee->setId(1);
        $employee->setFirstname('John');
        $employee->setLastname('Doe');
        $employee->setEmail('johndoe@example.com');
        $employee->setTelephoneNumber('123-456-7890');
        $employee->setCompany($company);

        $this->employeeServiceMock
            ->expects($this->once())
            ->method('get')
            ->with(1)
            ->willReturn($employee);

        $expectedResponseData = [
            'id' => 1,
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'johndoe@example.com',
            'telephone_number' => '123-456-7890',
            'company_id' => 1,
        ];

        $response = $this->employeeControllerMock->show(1);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertSame([$expectedResponseData], json_decode($response->getContent(), true));
    }

    public function testShowThrowsNotFoundHttpExceptionWhenEmployeeNotFound(): void
    {
        $this->employeeServiceMock
            ->expects($this->once())
            ->method('get')
            ->with(1)
            ->willReturn(null);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Not found employee with id: 1');

        $this->employeeControllerMock->show(1);
    }

    public function testCreateReturnsJsonResponseWhenEmployeeIsValid(): void
    {
        $employee = new Employee();
        $employee->setFirstname('John');
        $employee->setLastname('Doe');
        $employee->setEmail('john.doe@example.com');
        $employee->setTelephoneNumber('1234567890');
        $employee->setCompany(new Company());

        $request = new Request([], [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john.doe@example.com',
            'telephone_number' => '1234567890',
            'company_id' => 1,
        ]);

        $this->employeeServiceMock->expects($this->once())
            ->method('prepare')
            ->willReturn($employee);

        $this->baseValidatorMock
            ->expects($this->once())
            ->method('validate')
            ->with($employee)
            ->willReturn([]);

        $this->employeeServiceMock->expects($this->once())
            ->method('saveEntity')
            ->with($employee);

        $response = $this->employeeControllerMock->create($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals('"Created new employee successfully"', $response->getContent());
    }

    public function testUpdateReturnsJsonResponseWhenEmployeeIsValid(): void
    {
        $company = new Company();
        $company->setId(1);
        $company->setName('Acme Inc.');

        $employee = new Employee();
        $employee->setFirstname('John');
        $employee->setLastname('Doe');
        $employee->setEmail('john.doe@example.com');
        $employee->setTelephoneNumber('1234567890');
        $employee->setCompany($company);

        $requestContent = json_encode(
            [
                'firstname' => 'Jane',
                'lastname' => 'Doe',
                'email' => 'jane.doe@example.com',
                'telephone_number' => '0987654321',
                'company_id' => 1,
            ]);

        $request = new Request([], [], [], [], [], [], $requestContent);

        $this->employeeServiceMock
            ->expects($this->once())
            ->method('get')
            ->with(1)
            ->willReturn($employee);

        $this->employeeServiceMock
            ->expects($this->once())
            ->method('prepare')
            ->with(
                $employee,
                'Jane',
                'Doe',
                'jane.doe@example.com',
                '0987654321',
                1
            )->willReturn($employee);

        $this->baseValidatorMock
            ->expects($this->once())
            ->method('validate')
            ->with($employee)
            ->willReturn([]);

        $this->employeeServiceMock
            ->expects($this->once())
            ->method('saveEntity')
            ->with($employee);

        $response = $this->employeeControllerMock->update(1, $request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('"Updated employee successfully"', $response->getContent());
    }

    public function testDeleteReturnsNoContentWhenEmployeeIsDeleted(): void
    {
        $companyId = 123;
        $employeeMock = $this->createMock(Employee::class);

        $this->employeeServiceMock
            ->expects($this->once())
            ->method('get')
            ->with($companyId)
            ->willReturn($employeeMock);

        $this->employeeServiceMock
            ->expects($this->once())
            ->method('delete')
            ->with($employeeMock);

        $response = $this->employeeControllerMock->delete($companyId);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(JsonResponse::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}