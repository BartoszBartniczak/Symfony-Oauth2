<?php


namespace App\Infrastructure\Symfony\EventListener;


use App\Domain\Exception\DomainException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\VarDumper\VarDumper;

class HandlerFailedListener
{

    public function onKernelException(ExceptionEvent $event)
    {
        // You get the exception object from the received event
        $exception = $event->getThrowable();

        if (!$exception instanceof HandlerFailedException) {
            return;
        }

        $domainException = $exception->getPrevious();

        if (!$domainException instanceof DomainException) {
            return;
        }


        // Customize your response object to display the exception details
        $response = new JsonResponse(
            (object)[
                'errorMessage' => $this->getMessage($domainException),
            ],
        );

        // HttpExceptionInterface is a special type of exception that
        // holds status code and header details

        $response->setStatusCode(Response::HTTP_BAD_REQUEST);

        // sends the modified response object to the event
        $event->setResponse($response);
    }


    private function getMessage(DomainException $domainException): string
    {
        $message = $domainException->getMessage();
        if (!empty($message)) {
            return $message;
        }

        $class = get_class($domainException);
        $pieces = explode('\\', $class);
        $exceptionName = array_pop($pieces);
        $words = preg_split('/(?=[A-Z])/', $exceptionName);
        array_shift($words);
        array_walk($words, function (&$word, $index):void {

            if($index === 0){
                return;
            }

            $word = strtolower($word);
        });

        return join(' ', $words);
    }

}
