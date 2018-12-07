<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 03.12.18
 * Time: 12:16
 */

namespace App\Form\Api\Http\Resolvers;


use App\Form\Api\RequestDTOInterface;
use App\Helpers\ValidationHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class RequestResolver implements ArgumentValueResolverInterface
{
    /**
     * @var \Symfony\Component\Serializer\SerializerInterface
     */
    private $serializer;

    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    private $validator;

    public function __construct(
        \Symfony\Component\Serializer\SerializerInterface $serializer,
        \Symfony\Component\Validator\Validator\ValidatorInterface $validator
    ) {
        $this->serializer = $serializer;
        $this->validator = $validator;

    }
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        try {
            $reflection = new \ReflectionClass($argument->getType());
            if ($reflection->implementsInterface(RequestDTOInterface::class)) {
                return true;
            }
        } catch (\ReflectionException $exception) {}
          catch (\Exception $exception) {}
        return false;
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $class = $argument->getType();
        /** @var RequestDTOInterface $dto */
        $dto = new $class($request);
        //$dto = $this->serializer->deserialize($request->getContent(), $class, 'json');
        $constraints = $this->validator->validate($dto);
        if ($constraints->count() > 0) {
            $dto->setErrors(ValidationHelper::violationsToArray($constraints));
        }
        yield $dto;
    }

}