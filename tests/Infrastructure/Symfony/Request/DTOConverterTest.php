<?php

namespace App\Tests\Infrastructure\Symfony\Request;

use App\Application\DTO\CreateUserDTO;
use App\Application\DTO\DataTransferObject;
use App\Infrastructure\Symfony\Exception\ValidationException;
use App\Infrastructure\Symfony\Request\DTOConverter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @coversDefaultClass \App\Infrastructure\Symfony\Request\DTOConverter
 */
class DTOConverterTest extends TestCase
{

    private DTOConverter $converter;
    private MockObject|ValidatorInterface $validator;
    private SerializerInterface|MockObject $serializer;

    protected function setUp(): void
    {
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->converter = new DTOConverter($this->serializer, $this->validator);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $this->assertInstanceOf(ParamConverterInterface::class, $this->converter);
    }

    /**
     * @covers ::apply
     */
    public function testApply()
    {
        $dto = $this->createMock(DataTransferObject::class);

        $configuration = $this->createMock(ParamConverter::class);
        $configuration->method('getName')->willReturn('dto');
        $configuration->method('getClass')->willReturn(DataTransferObject::class);

        $request = $this->getMockBuilder(Request::class)
            ->onlyMethods(['getContent'])
            ->getMock();
        $request->method('getContent')->willReturn('{"dto": true}');

        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->with('{"dto": true}', DataTransferObject::class, 'json')
            ->willReturn($dto);

        $violationList = $this->createMock(ConstraintViolationList::class);
        $violationList->method('count')->willReturn(0);

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($dto)
            ->willReturn($violationList);

        $this->converter->apply($request, $configuration);
        $this->assertSame($dto, $request->attributes->get('dto'));
    }

    /**
     * @covers ::apply
     */
    public function testApplyThrowsExceptionIfThereAreValidationViolationList(){
        $this->expectException(ValidationException::class);
        
        $dto = $this->createMock(DataTransferObject::class);

        $configuration = $this->createMock(ParamConverter::class);
        $configuration->method('getName')->willReturn('dto');
        $configuration->method('getClass')->willReturn(DataTransferObject::class);

        $request = $this->getMockBuilder(Request::class)
            ->onlyMethods(['getContent'])
            ->getMock();
        $request->method('getContent')->willReturn('{"dto": true}');

        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->with('{"dto": true}', DataTransferObject::class, 'json')
            ->willReturn($dto);

        $violationList = $this->createMock(ConstraintViolationList::class);
        $violationList->method('count')->willReturn(1);

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($dto)
            ->willReturn($violationList);

        $this->converter->apply($request, $configuration);
        $expectedException = $this->getExpectedException();
        assert($expectedException instanceof ValidationException);
        $this->assertSame($violationList, $expectedException->getConstraintViolationList());
    }

    /**
     * @covers ::supports
     */
    public function testSupports()
    {
        $configuration = $this->createMock(ParamConverter::class);
        $configuration->method('getClass')->willReturn(DataTransferObject::class, \DateTime::class);
        $this->assertTrue($this->converter->supports($configuration));
        $this->assertFalse($this->converter->supports($configuration));
    }


}
