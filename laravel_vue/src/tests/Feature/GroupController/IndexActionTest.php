<?php

declare(strict_types=1);

namespace Tests\Feature\GroupController;

use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexActionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Group $group
     */
    private Group $group;

    /**
     * @var GroupUser $group_user
     */
    private GroupUser $group_user;

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

        $this->user = User::factory()->create();
        $this->group = Group::factory()->create([
            'owner_user_id' => $this->user->id
        ]);
        $this->group_user = GroupUser::create([
            'user_id' => $this->user->id,
            'group_id' => $this->group->id
        ]);
    }

    /**
     * @return void
     */
    public function testGetGroup(): void
    {
        // リクエスト
        $response = $this->actingAs($this->user)->getJson('/api/groups');

        // 検証
        $response->assertOk()
            ->assertJsonStructure([
                'groups' => [
                    [
                        'id',
                        'name',
                        'owner_user_id',
                        'created_at',
                        'updated_at',
                        'deleted_at',
                        'pivot' => [
                            'user_id',
                            'group_id',
                        ]
                    ]
                ],
            ])
            ->assertExactJson([
                'groups' => [
                    [
                        'id' => $this->group->id,
                        'name' => $this->group->name,
                        'owner_user_id' => $this->user->id,
                        'created_at' => (string)$this->group->created_at,
                        'updated_at' => (string)$this->group->updated_at,
                        'deleted_at' => null,
                        'pivot' => [
                            'user_id' => $this->user->id,
                            'group_id' => $this->group->id,
                        ]
                    ]
                ],
            ]);

    }
}