<?php


namespace App\Infrastructure\Symfony\Request;


use App\Infrastructure\Symfony\Exception\ValidationException;
use App\Application\DTO\DataTransferObject;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DTOConverter implements ParamConverterInterface
{

    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public function apply(Request $request, ParamConverter $configuration)
    {
        $name = $configuration->getName();
        $class = $configuration->getClass();

        $object = $this->serializer->deserialize($request->getContent(), $class, 'json');

        $violations = $this->validator->validate($object);
        if (count($violations) > 0) {
            throw new ValidationException($violations);
        }

        $request->attributes->set($name, $object);

        return true;
    }

    public function supports(ParamConverter $configuration)
    {
        return is_a($configuration->getClass(), DataTransferObject::class, true);
    }


}
