<?php

use App\Models\Role;
use App\Models\User;

test('admin users can view the admin dashboard', function () {
    $adminRole = Role::create(['name' => 'Admin']);
    $admin = User::factory()->create(['role_id' => $adminRole->id]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Operations Dashboard');
});

test('customer users cannot view the admin dashboard', function () {
    $customerRole = Role::create(['name' => 'Customer']);
    $customer = User::factory()->create(['role_id' => $customerRole->id]);

    $this->actingAs($customer)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

test('guests are redirected away from the admin dashboard', function () {
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('login'));
});
