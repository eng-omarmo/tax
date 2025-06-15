<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponseTrait
{
    /**
     * Base response format.
     */
    protected function response(
        mixed $data = null,
        int $statusCode = Response::HTTP_OK,
        string $message = '',
        bool $success = false
    ): JsonResponse {
        $message = $message ?: Response::$statusTexts[$statusCode] ?? 'Unknown status';

        return response()->json([
            'success' => $success,
            'code' => $statusCode,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Generic success response.
     */
    protected function successResponse(
        mixed $data = null,
        int $statusCode = Response::HTTP_OK,
        string $message = ''
    ): JsonResponse {
        return $this->response($data, $statusCode, $message, true);
    }

    /**
     * Generic error response.
     */
    protected function errorResponse(
        mixed $errors = null,
        int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR,
        string $message = ''
    ): JsonResponse {
        return $this->response($errors, $statusCode, $message, false);
    }

    // === Shortcuts ===

    protected function okResponse(mixed $data = null, string $message = ''): JsonResponse
    {
        return $this->successResponse($data, Response::HTTP_OK, $message);
    }

    protected function createdResponse(mixed $data = null, string $message = ''): JsonResponse
    {
        return $this->successResponse($data, Response::HTTP_CREATED, $message);
    }

    protected function badRequestResponse(mixed $data = null, string $message = ''): JsonResponse
    {
        return $this->errorResponse($data, Response::HTTP_BAD_REQUEST, $message);
    }

    protected function unauthorizedResponse(mixed $data = null, string $message = ''): JsonResponse
    {
        return $this->errorResponse($data, Response::HTTP_UNAUTHORIZED, $message);
    }

    protected function forbiddenResponse(mixed $data = null, string $message = ''): JsonResponse
    {
        return $this->errorResponse($data, Response::HTTP_FORBIDDEN, $message);
    }

    protected function notFoundResponse(mixed $data = null, string $message = ''): JsonResponse
    {
        return $this->errorResponse($data, Response::HTTP_NOT_FOUND, $message);
    }

    protected function conflictResponse(mixed $data = null, string $message = ''): JsonResponse
    {
        return $this->errorResponse($data, Response::HTTP_CONFLICT, $message);
    }

    protected function unprocessableResponse(mixed $data = null, string $message = ''): JsonResponse
    {
        return $this->errorResponse($data, Response::HTTP_UNPROCESSABLE_ENTITY, $message);
    }

    protected function methodNotAllowedResponse(mixed $data = null, string $message = ''): JsonResponse
    {
        return $this->errorResponse($data, Response::HTTP_METHOD_NOT_ALLOWED, $message);
    }

    protected function serviceUnavailableResponse(mixed $data = null, string $message = ''): JsonResponse
    {
        return $this->errorResponse($data, Response::HTTP_SERVICE_UNAVAILABLE, $message);
    }
}
