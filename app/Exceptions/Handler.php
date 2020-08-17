<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

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
   * @param \Throwable $exception
   * @return void
   *
   * @throws \Exception
   */
  public function report(Throwable $exception)
  {
    parent::report($exception);
  }

  /**
   * Render an exception into an HTTP response.
   *
   * @param \Illuminate\Http\Request $request
   * @param \Throwable $exception
   * @return \Symfony\Component\HttpFoundation\Response
   *
   * @throws \Throwable
   */
  public function render($request, Throwable $exception)
  {
    if ($exception instanceof ValidationException) {
      return $this->convertValidationExceptionToResponse($exception, $request);
    }

    if ($exception instanceof ModelNotFoundException) {
      $modelName = strtolower(class_basename($exception->getModel()));

      return $this->errorResponse(
      "Does not exist any '{$modelName}' with the specified identifier",
      404);
    }

    if ($exception instanceof AuthenticationException) {
      return $this->unauthenticated($request, $exception);
    }

    if ($exception instanceof AuthorizationException) {
      return $this->errorResponse($exception->getMessage(), 403);
    }

    if ($exception instanceof NotFoundHttpException) {
      return $this->errorResponse(
        'The specified URL cannot be found.',
        404);
    }

    if ($exception instanceof MethodNotAllowedHttpException) {
      return $this->errorResponse(
        'The specified method for the request is invalid.',
        405);
    }

    if ($exception instanceof HttpException) {
      return $this->errorResponse(
        $exception->getMessage(),
        $exception->getStatusCode()
        );
    }

    if ($exception instanceof QueryException) {
      $errorCode = $exception->errorInfo[1];

      if ($errorCode == 1451) {
        return $this->errorResponse(
          'Cannot remove this resource permanently. It is related wit other resource.',
          409
        );
      }

    }

    if ($exception instanceof TokenMismatchException) {
      return redirect()->back()->withInput($request->input());
    }

    if (config('app.debug')) {
      /// Returns the detailed message if the debug mod is on.
      return parent::render($request, $exception);
    }

    return $this->errorResponse(
      'Unexpected Exception. Please try later',
      500
      );
  }

  /// Atila. Overwriting the parent function for handling
  /// Authentication Exceptions:
  protected function unauthenticated($request, AuthenticationException $exception)
  {
    if ($this->isFrontend($request)) {
      return redirect()->guest('login');
    }
    return $this->errorResponse('Unauthenticated.', 401);
  }

  /**
   * Create a response object from the given validation exception.
   *
   * @param \Illuminate\Validation\ValidationException $e
   * @param \Illuminate\Http\Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  /// Atila. Modifying this method to return errors as a
  /// JSON response independently if it is requesting JSON or not.
  protected function convertValidationExceptionToResponse(ValidationException $e, $request)
  {
    /// Version 1:
//    if ($e->response) {
//      return $e->response;
//    }
//    return $this->invalidJson($request, $e);

    /// Version 2 (Laravel 5.*):
    $errors = $e->validator->errors()->getMessages();

    if ($this->isFrontend($request)) {
      return $request->ajax() ?
        response()->json($errors, 422) :
        redirect()
          ->back()
          ->withInput($request->input)
          ->withErrors($errors);
    }

    // return response()->json($errors, 422);
    return $this->errorResponse($errors, 422);
  }

  private function isFrontend($request)
  {
    return $request->acceptsHtml() &&
      collect($request->route()->middleware())
        ->contains('web');

  }


}
