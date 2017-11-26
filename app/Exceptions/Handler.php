<?php

namespace App\Exceptions;

use Exception;
use App\Traits\ApiResponser;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\QueryException;

class Handler extends ExceptionHandler
{
    use ApiResponser;
  
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
        {
            parent::report($exception);
        }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        //json response for every exception

        if($exception instanceof ValidationException)
            {
                return $this->convertValidationExceptionToResponse($exception, $request);
            }

        //for model not found response 

        if($exception instanceof ModelNotFoundException)
            {
                $modelName = strtolower(class_basename($exception->getModel()));
                return $this->errorResponse("Doesnot exist any {$modelName } with the specified identifier",404);
            }

        //Handling authentication exception

        if($exception instanceof AuthenticationException)
            {
                return $this->unauthenticated($request, $exception);
            }

        //handling authorization exception
        if($exception instanceof AuthorizationException)
            {
            return  $this->errorResponse($exception->getMessage(),403);
            }

        //Not found http exception
        if($exception instanceof NotFoundHttpException)
            {
            return  $this->errorResponse('The specified URL cannot be found ',404);
            }

         //Method not allowed
         if($exception instanceof MethodNotAllowedHttpException)
            {
            return  $this->errorResponse('The specified method for the request is invalid ',405);
            }

         if($exception instanceof HttpException)
            {
            return  $this->errorResponse($exception->getMessage(),$exception->getStatusCode());
            }

         if($exception instanceof QueryException)
         {
                $errorCode = $exception->errorInfo[1];
                if($errorCode == 1451)
                {
                    return $this->errorResponse('Cannot remove this resource permanently. It is related to another resource',409);
                }
         }
       
         //if debug is true
         if(config('app.debug'))
            {
                return parent::render($request, $exception); 
            }

         return $this->errorResponse('Unexpected Exception. Try later',500);

      }
    
        

     /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
        {
            return $this->errorResponse('unauthenticated',401);
        
        }



    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
        {  
            $errors = $e->validator->errors()->getMessages();
            return $this->errorResponse($errors,422);
            
        }


}
