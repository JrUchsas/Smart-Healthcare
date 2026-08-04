<?php

use App\Models\ActivityLog;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $this->adminUser = User::factory()->create(['user_type' => 'admin']);
    $this->adminUser->assignRole('Admin');
});

test('admin can delete selected activity logs', function () {
    $log1 = ActivityLog::log('Event 1', 'Description 1', $this->adminUser);
    $log2 = ActivityLog::log('Event 2', 'Description 2', $this->adminUser);
    $log3 = ActivityLog::log('Event 3', 'Description 3', $this->adminUser);

    $response = $this->actingAs($this->adminUser)
        ->delete(route('admin.activity-logs.destroy-selected'), [
            'ids' => [$log1->id, $log2->id],
        ]);

    $response->assertRedirect(route('admin.activity-logs.index'));
    $this->assertDatabaseMissing('activity_logs', ['id' => $log1->id]);
    $this->assertDatabaseMissing('activity_logs', ['id' => $log2->id]);
    $this->assertDatabaseHas('activity_logs', ['id' => $log3->id]);
});

test('admin can clear all activity logs', function () {
    ActivityLog::log('Event 1', 'Description 1', $this->adminUser);
    ActivityLog::log('Event 2', 'Description 2', $this->adminUser);

    $response = $this->actingAs($this->adminUser)
        ->delete(route('admin.activity-logs.clear'));

    $response->assertRedirect(route('admin.activity-logs.index'));
    expect(ActivityLog::count())->toBe(0);
});
