<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
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

    return parent::render($request, $exception);
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
    // return response()->json($errors, 422);
    return $this->errorResponse($errors, 422);
  }
}
