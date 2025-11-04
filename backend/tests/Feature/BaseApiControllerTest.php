<?php

namespace Tests\Feature;

use App\Http\Controllers\BaseApiController;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class BaseApiControllerTest extends TestCase
{
    use RefreshDatabase;

    private $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new class extends BaseApiController {
            // Expose protected methods for testing
            public function testExecuteWithExceptionHandling(callable $callback, string $context = ''): JsonResponse
            {
                return $this->executeWithExceptionHandling($callback, $context);
            }

            public function testSuccessResponse($data = null, ?string $message = null, int $statusCode = 200): JsonResponse
            {
                return $this->successResponse($data, $message, $statusCode);
            }

            public function testErrorResponse(string $message, int $statusCode = 500, $errors = null): JsonResponse
            {
                return $this->errorResponse($message, $statusCode, $errors);
            }

            public function testRespondWithResource($resource, ?callable $transformer = null): JsonResponse
            {
                return $this->respondWithResource($resource, $transformer);
            }

            public function testRespondWithCollection($collection, ?callable $transformer = null): JsonResponse
            {
                return $this->respondWithCollection($collection, $transformer);
            }

            public function testCreatedResponse($data, ?string $message = null): JsonResponse
            {
                return $this->createdResponse($data, $message);
            }

            public function testForbiddenResponse(?string $message = null): JsonResponse
            {
                return $this->forbiddenResponse($message);
            }

            public function testUnauthorizedResponse(?string $message = null): JsonResponse
            {
                return $this->unauthorizedResponse($message);
            }
        };
    }

    /** @test */
    public function it_can_execute_callback_successfully()
    {
        $response = $this->controller->testExecuteWithExceptionHandling(function () {
            return $this->controller->testSuccessResponse(['test' => 'data']);
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['data' => ['test' => 'data']], json_decode($response->getContent(), true));
    }

    /** @test */
    public function it_handles_model_not_found_exception()
    {
        $response = $this->controller->testExecuteWithExceptionHandling(function () {
            throw new ModelNotFoundException();
        });

        $this->assertEquals(404, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Resource not found.', $data['message']);
    }

    /** @test */
    public function it_handles_validation_exception()
    {
        $validator = \Validator::make([], ['name' => 'required']);

        try {
            $validator->validate();
        } catch (ValidationException $e) {
            $response = $this->controller->testExecuteWithExceptionHandling(function () use ($e) {
                throw $e;
            });

            $this->assertEquals(422, $response->getStatusCode());
            $data = json_decode($response->getContent(), true);
            $this->assertEquals('Validation failed.', $data['message']);
            $this->assertArrayHasKey('errors', $data);
        }
    }

    /** @test */
    public function it_handles_general_exception_with_debug_off()
    {
        Config::set('app.debug', false);

        $response = $this->controller->testExecuteWithExceptionHandling(function () {
            throw new \Exception('Test error message');
        });

        $this->assertEquals(500, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('An error occurred while processing your request.', $data['message']);
        $this->assertEquals('Internal server error', $data['error']);
    }

    /** @test */
    public function it_handles_general_exception_with_debug_on()
    {
        Config::set('app.debug', true);

        $response = $this->controller->testExecuteWithExceptionHandling(function () {
            throw new \Exception('Test error message');
        });

        $this->assertEquals(500, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('An error occurred while processing your request.', $data['message']);
        $this->assertEquals('Test error message', $data['error']);
    }

    /** @test */
    public function it_returns_success_response_with_data()
    {
        $response = $this->controller->testSuccessResponse(['test' => 'value']);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(['data' => ['test' => 'value']], $data);
    }

    /** @test */
    public function it_returns_success_response_with_message()
    {
        $response = $this->controller->testSuccessResponse(['test' => 'value'], 'Operation successful');

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Operation successful', $data['message']);
        $this->assertEquals(['test' => 'value'], $data['data']);
    }

    /** @test */
    public function it_returns_success_response_with_custom_status_code()
    {
        $response = $this->controller->testSuccessResponse(['test' => 'value'], null, 202);

        $this->assertEquals(202, $response->getStatusCode());
    }

    /** @test */
    public function it_returns_error_response()
    {
        $response = $this->controller->testErrorResponse('Error occurred', 400);

        $this->assertEquals(400, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Error occurred', $data['message']);
    }

    /** @test */
    public function it_returns_error_response_with_errors()
    {
        $errors = ['field' => ['Field is required']];
        $response = $this->controller->testErrorResponse('Validation failed', 422, $errors);

        $this->assertEquals(422, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Validation failed', $data['message']);
        $this->assertEquals($errors, $data['errors']);
    }

    /** @test */
    public function it_responds_with_resource_without_transformer()
    {
        $student = Student::factory()->create();

        $response = $this->controller->testRespondWithResource($student);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $data);
        $this->assertEquals($student->id, $data['data']['id']);
    }

    /** @test */
    public function it_responds_with_resource_with_transformer()
    {
        $student = Student::factory()->create();

        $response = $this->controller->testRespondWithResource($student, function ($student) {
            return [
                'student_id' => $student->id,
                'full_name' => $student->name . ' ' . $student->surname,
            ];
        });

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $data);
        $this->assertEquals($student->id, $data['data']['student_id']);
        $this->assertEquals($student->name . ' ' . $student->surname, $data['data']['full_name']);
    }

    /** @test */
    public function it_responds_with_collection_without_transformer()
    {
        $students = Student::factory()->count(3)->create();

        $response = $this->controller->testRespondWithCollection($students);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $data);
        $this->assertCount(3, $data['data']);
    }

    /** @test */
    public function it_responds_with_collection_with_transformer()
    {
        $students = Student::factory()->count(2)->create();

        $response = $this->controller->testRespondWithCollection($students, function ($student) {
            return [
                'student_id' => $student->id,
                'full_name' => $student->name . ' ' . $student->surname,
            ];
        });

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $data);
        $this->assertCount(2, $data['data']);
        $this->assertArrayHasKey('student_id', $data['data'][0]);
        $this->assertArrayHasKey('full_name', $data['data'][0]);
    }

    /** @test */
    public function it_returns_created_response()
    {
        $response = $this->controller->testCreatedResponse(['id' => 1]);

        $this->assertEquals(201, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(['id' => 1], $data['data']);
    }

    /** @test */
    public function it_returns_created_response_with_message()
    {
        $response = $this->controller->testCreatedResponse(['id' => 1], 'Resource created');

        $this->assertEquals(201, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Resource created', $data['message']);
        $this->assertEquals(['id' => 1], $data['data']);
    }

    /** @test */
    public function it_returns_forbidden_response()
    {
        $response = $this->controller->testForbiddenResponse();

        $this->assertEquals(403, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Forbidden.', $data['message']);
    }

    /** @test */
    public function it_returns_forbidden_response_with_custom_message()
    {
        $response = $this->controller->testForbiddenResponse('You cannot access this resource');

        $this->assertEquals(403, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('You cannot access this resource', $data['message']);
    }

    /** @test */
    public function it_returns_unauthorized_response()
    {
        $response = $this->controller->testUnauthorizedResponse();

        $this->assertEquals(401, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Unauthorized.', $data['message']);
    }

    /** @test */
    public function it_returns_unauthorized_response_with_custom_message()
    {
        $response = $this->controller->testUnauthorizedResponse('Authentication required');

        $this->assertEquals(401, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Authentication required', $data['message']);
    }

    /** @test */
    public function it_logs_context_in_exception_handling()
    {
        \Log::shouldReceive('error')
            ->once()
            ->with(\Mockery::on(function ($message) {
                return str_contains($message, 'Error fetching data:') &&
                       str_contains($message, 'Test exception');
            }));

        Config::set('app.debug', false);

        $response = $this->controller->testExecuteWithExceptionHandling(function () {
            throw new \Exception('Test exception');
        }, 'fetching data');

        $this->assertEquals(500, $response->getStatusCode());
    }
}
