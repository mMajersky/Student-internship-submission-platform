<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

abstract class BaseApiController extends Controller
{
    /**
     * Standard success JSON response
     *
     * @param mixed $data
     * @param string|null $message
     * @param int $code
     * @return JsonResponse
     */
    protected function successResponse($data, ?string $message = null, int $code = 200): JsonResponse
    {
        $response = ['data' => $data];

        if ($message) {
            $response['message'] = $message;
        }

        return response()->json($response, $code);
    }

    /**
     * Standard error JSON response
     *
     * @param string $message
     * @param int $code
     * @param array|string|null $errors Can be array for validation errors or string for general error detail
     * @return JsonResponse
     */
    protected function errorResponse(string $message, int $code = 500, $errors = null): JsonResponse
    {
        $response = ['message' => $message];

        if ($errors !== null) {
            if (is_array($errors)) {
                $response['errors'] = $errors;
            } else {
                $response['error'] = $errors;
            }
        }

        return response()->json($response, $code);
    }

    /**
     * Handle ModelNotFoundException
     *
     * @param string $modelName
     * @return JsonResponse
     */
    protected function handleModelNotFound(string $modelName = 'Resource'): JsonResponse
    {
        return $this->errorResponse("{$modelName} not found.", 404);
    }

    /**
     * Handle general exception
     *
     * @param \Exception $e
     * @param string $context
     * @return JsonResponse
     */
    protected function handleException(\Exception $e, string $context = ''): JsonResponse
    {
        $logMessage = $context ? "Error {$context}: " : "Error: ";
        Log::error($logMessage . $e->getMessage());

        $errorDetail = config('app.debug') ? $e->getMessage() : 'Internal server error';

        return $this->errorResponse(
            'An error occurred while processing your request.',
            500,
            $errorDetail
        );
    }

    /**
     * Execute with standard exception handling
     *
     * @param callable $callback
     * @param string $context
     * @return JsonResponse
     */
    protected function executeWithExceptionHandling(callable $callback, string $context = ''): JsonResponse
    {
        try {
            return $callback();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->handleModelNotFound();
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse(
                'Validation failed.',
                422,
                $e->errors()
            );
        } catch (\Exception $e) {
            return $this->handleException($e, $context);
        }
    }

    /**
     * Format a single resource response
     *
     * @param mixed $resource
     * @param callable|null $transformer
     * @return JsonResponse
     */
    protected function respondWithResource($resource, ?callable $transformer = null): JsonResponse
    {
        $data = $transformer ? $transformer($resource) : $resource;
        return $this->successResponse($data);
    }

    /**
     * Format a collection response
     *
     * @param \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection $collection
     * @param callable|null $transformer
     * @return JsonResponse
     */
    protected function respondWithCollection($collection, ?callable $transformer = null): JsonResponse
    {
        $data = $transformer ? $collection->map($transformer) : $collection;
        return $this->successResponse($data);
    }

    /**
     * Created response (201)
     *
     * @param mixed $data
     * @param string|null $message
     * @return JsonResponse
     */
    protected function createdResponse($data, ?string $message = 'Resource created successfully.'): JsonResponse
    {
        return $this->successResponse($data, $message, 201);
    }

    /**
     * No content response (204)
     *
     * @return JsonResponse
     */
    protected function noContentResponse(): JsonResponse
    {
        return response()->json(null, 204);
    }

    /**
     * Forbidden response (403)
     *
     * @param string|null $message
     * @return JsonResponse
     */
    protected function forbiddenResponse(?string $message = null): JsonResponse
    {
        return $this->errorResponse($message ?? 'Forbidden.', 403);
    }

    /**
     * Unauthorized response (401)
     *
     * @param string|null $message
     * @return JsonResponse
     */
    protected function unauthorizedResponse(?string $message = null): JsonResponse
    {
        return $this->errorResponse($message ?? 'Unauthorized.', 401);
    }
}
