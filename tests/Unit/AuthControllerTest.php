<?php

use App\Http\Controllers\AuthController;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Mockery\MockInterface;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testRegisterSuccessfully()
    {
        $userRepositoryMock = $this->mock(UserRepository::class);
        $authController = new AuthController($userRepositoryMock);
        $request = new Request([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $userRepositoryMock->shouldReceive('create')
            ->once()
            ->with([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'password123',
            ])
            ->andReturn(true);

        $response = $authController->register($request);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals(['message' => 'Successfully registered'], json_decode($response->getContent(), true));
    }

    public function testRegisterException()
    {
        $userRepositoryMock = $this->mock(UserRepository::class);
        $authController = new AuthController($userRepositoryMock);
        $request = new Request([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $userRepositoryMock->shouldReceive('create')
            ->once()
            ->andThrow(new \Exception('Database error'));

        $response = $authController->register($request);

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertEquals(['error' => 'Failed to register user'], json_decode($response->getContent(), true));
    }

    public function testLoginSuccessfully()
    {
        $userRepositoryMock = $this->mock(UserRepository::class);
        $authController = new AuthController($userRepositoryMock);
        $request = new Request([
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $userRepositoryMock->shouldNotReceive('create');
        JWTAuth::shouldReceive('attempt')
            ->once()
            ->with([
                'email' => 'john@example.com',
                'password' => 'password123',
            ])
            ->andReturn('fake_token');

        $response = $authController->login($request);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(['token' => 'fake_token'], json_decode($response->getContent(), true));
    }

    public function testLoginInvalidCredentials()
    {
        $userRepositoryMock = $this->mock(UserRepository::class);
        $authController = new AuthController($userRepositoryMock);
        $request = new Request([
            'email' => 'john@example.com',
            'password' => 'invalid_password',
        ]);

        $userRepositoryMock->shouldNotReceive('create');
        JWTAuth::shouldReceive('attempt')
            ->once()
            ->with([
                'email' => 'john@example.com',
                'password' => 'invalid_password',
            ])
            ->andReturn(false);

        $response = $authController->login($request);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals(['error' => 'Invalid credentials'], json_decode($response->getContent(), true));
    }

    public function testLoginException()
    {
        $userRepositoryMock = $this->mock(UserRepository::class);
        $authController = new AuthController($userRepositoryMock);
        $request = new Request([
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $userRepositoryMock->shouldNotReceive('create');
        JWTAuth::shouldReceive('attempt')
            ->once()
            ->with([
                'email' => 'john@example.com',
                'password' => 'password123',
            ])
            ->andThrow(new \Exception('Authentication error'));

        $response = $authController->login($request);

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertEquals(['error' => 'Failed to login'], json_decode($response->getContent(), true));
    }
}
