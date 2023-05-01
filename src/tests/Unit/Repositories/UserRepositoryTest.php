<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var UserRepository $user_repository
     */
    private UserRepository $user_repository;

    /**
     * @var User $user
     */
    private User $user;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user_repository = new UserRepository();

        $this->user = User::factory()->create();
    }

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $expected_name = 'ユーザー名';
        $expected_email = 'test@example.com';
        $expected_password = 'password';

        $user = $this->user_repository->create([
            'name' => $expected_name,
            'email' => $expected_email,
            'password' => $expected_password,
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame($expected_name, $user->name);
        $this->assertSame($expected_email, $user->email);
        $this->assertSame($expected_password, $user->password);
    }

    /**
     * @return void
     */
    public function testFindById(): void
    {
        $expected_id = $this->user->id;
        $expected_name = $this->user->name;
        $expected_email = $this->user->email;

        $user = $this->user_repository->findById($expected_id);

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame($expected_id, $user->id);
        $this->assertSame($expected_name, $user->name);
        $this->assertSame($expected_email, $user->email);
    }
}
