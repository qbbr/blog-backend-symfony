<?php

namespace App\EventSubscriber;

use App\Exceptions\ValidationFailException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExceptionToJsonResponseSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $statusCode = $this->getStatusCodeFromException($exception);
        $data = [
            'code' => $statusCode,
            'message' => $exception->getMessage(),
        ];

        if ($exception instanceof ValidationFailException) {
            $data['errors'] = $exception->getErrors();
        }

        if ('prod' !== $event->getRequest()->server->get('APP_ENV')) {
            // TODO: Do not use this in production! This will potentially leak sensitive information.
            $data['type'] = $this->getErrorTypeFromException($exception);
            $data['file'] = $exception->getFile();
            $data['line'] = $exception->getLine();
            $data['trace'] = $exception->getTrace();
        }

        $response = new JsonResponse($data, $statusCode);
        $response->headers->set('Content-Type', 'application/problem+json');
        $event->setResponse($response);
    }

    private function getStatusCodeFromException(\Throwable $exception): int
    {
        if ($exception instanceof HttpException) {
            return $exception->getStatusCode();
        }

        return 500;
    }

    private function getErrorTypeFromException(\Throwable $exception): string
    {
        $parts = explode('\\', \get_class($exception));

        return end($parts);
    }
}
