<?php


namespace App\Infrastructure\Symfony\EventListener;


use App\Application\Exception\CommandHandlerFailed;
use App\Domain\Exception\DomainException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\VarDumper\VarDumper;

final class HandlerFailedListener
{

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        
        if(!$exception instanceof CommandHandlerFailed){
            return;
        }
        
        $handlerFailed = $exception->getPrevious();
        
        if (!$handlerFailed instanceof HandlerFailedException) {
            return;
        }

        $domainException = $handlerFailed->getPrevious();

        if (!$domainException instanceof DomainException) {
            return;
        }

        $response = new JsonResponse(
            (object)[
                'errorMessage' => $this->getMessage($domainException),
            ],
        );

        $response->setStatusCode(Response::HTTP_BAD_REQUEST);
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
