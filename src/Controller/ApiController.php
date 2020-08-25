<?php

namespace App\Controller;

use App\Annotation\Api;
use App\Exceptions\ValidationFailException;
use App\Pagination\Paginator;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class ApiController extends AbstractController
{
    protected ValidatorInterface $validator;

    public function __construct(
        ValidatorInterface $validator
    ) {
        $this->validator = $validator;
    }

    protected function transformJsonContent(Request $request): Request
    {
        $data = json_decode($request->getContent(), true);

        if (null === $data) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }

    protected function validateObject($object): void
    {
        $errors = $this->validator->validate($object);

        if (0 !== $errors->count()) {
            throw new ValidationFailException($this->getValidationErrors($errors));
        }
    }

    protected function getValidationErrors(ConstraintViolationListInterface $violationList): array
    {
        $errors = [];

        /** @var ConstraintViolationInterface $violation */
        foreach ($violationList as $violation) {
            $errors[$violation->getPropertyPath()][] = $violation->getMessage();
        }

        return $errors;
    }

    protected function fillObject($object, $data): void
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        foreach ($data as $name => $value) {
            $reflectionClass = new \ReflectionClass($object);
            $property = $reflectionClass->getProperty($name);
            $reader = new AnnotationReader();
            /** @var Api|null $annotation */
            $annotation = $reader->getPropertyAnnotation($property, Api::class);

            if (null !== $annotation && $annotation->write) {
                $propertyAccessor->setValue($object, $name, $value);
            } else {
                throw new UnprocessableEntityHttpException(sprintf('Property "%s" is not writable', $name));
            }
        }
    }

    protected function normalize($object, array $groups = [])
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $serializer = new Serializer([
            new DateTimeNormalizer(/*[DateTimeNormalizer::FORMAT_KEY => 'H:i:s d.m.Y']*/),
            new ObjectNormalizer($classMetadataFactory),
        ]);

        return $serializer->normalize($object, null, ['groups' => $groups]);
    }

    protected function renderPaginator(Paginator $paginator, array $normalizerGroups = []): array
    {
        $result = $paginator->getResults();

        return [
            'results' => $this->normalize($result, $normalizerGroups),
            'page' => $paginator->getCurrentPage(),
            'count' => \count($result),
            'pageSize' => $paginator->getPageSize(),
            'total' => $paginator->getNumResults(),
        ];
    }
}
