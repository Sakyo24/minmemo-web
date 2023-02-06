<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\Admin;
use App\Repositories\AdminRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var AdminRepository $admin_repository
     */
    private AdminRepository $admin_repository;

    /**
     * @var Admin $admin
     */
    private Admin $admin;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->admin_repository = new AdminRepository();

        $this->admin = Admin::factory()->create();
    }

    /**
     * @return void
     */
    public function testFindById(): void
    {
        $expected_id = $this->admin->id;
        $expected_name = $this->admin->name;
        $expected_email = $this->admin->email;

        $admin = $this->admin_repository->findById($expected_id);

        $this->assertInstanceOf(Admin::class, $admin);
        $this->assertSame($expected_id, $admin->id);
        $this->assertSame($expected_name, $admin->name);
        $this->assertSame($expected_email, $admin->email);
    }
}
