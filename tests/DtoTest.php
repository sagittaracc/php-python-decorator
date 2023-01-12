<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Sagittaracc\PhpPythonDecorator\tests\classes\Data;
use Sagittaracc\PhpPythonDecorator\tests\decorators\CreateObjectDto;
use Sagittaracc\PhpPythonDecorator\tests\exceptions\DtoException;
use Sagittaracc\PhpPythonDecorator\tests\exceptions\DtoTypeError;
use Sagittaracc\PhpPythonDecorator\tests\exceptions\DtoValidationError;

final class DtoTest extends TestCase
{
    public function testDto(): void
    {
        $data = new Data();
        $dtoData = $data->getData();

        $this->assertInstanceOf(CreateObjectDto::class, $dtoData);
        $this->assertSame(1, $dtoData->dtoId);
        $this->assertSame('name', $dtoData->dtoName);
        $this->assertSame('caption', $dtoData->dtoCaption);
    }

    public function testFailDto(): void
    {
        $this->expectException(DtoException::class);
        $this->expectExceptionMessage('Sagittaracc\PhpPythonDecorator\tests\decorators\CreateObjectDto::$dtoCaption can not be set because in method Sagittaracc\PhpPythonDecorator\tests\classes\Data::getFailData() property `caption` was not returned!');

        $data = new Data();
        $data->getFailData();
    }

    public function testUnvalidDto(): void
    {
        $this->expectException(DtoTypeError::class);

        $data = new Data();
        $data->getUnvalidData();
    }

    public function testUnvalidDtoOverCustomValidation(): void
    {
        $this->expectException(DtoValidationError::class);
        $this->expectExceptionMessage('CreateObjectDto::$dtoId should be positive!');

        $data = new Data();
        $data->getUnvalidDataOverCustomValidation();
    }

    public function testNestedDto(): void
    {
        $data = new Data();
        $users = $data->getUserList();

        $this->assertSame(['me', 'friend'], $users->list);
        $this->assertSame(['guest', 'user', 'admin'], $users->roles->list);
    }
}