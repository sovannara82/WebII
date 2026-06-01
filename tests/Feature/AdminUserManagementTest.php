<?php

use App\Models\Role;
use App\Models\User;

function actingAdmin(): User
{
    $adminRole = Role::create(['name' => 'Admin']);

    return User::factory()->create([
        'role_id' => $adminRole->id,
        'email' => 'admin@example.com',
    ]);
}

test('admin creates customers only from the users screen', function () {
    $admin = actingAdmin();
    $customerRole = Role::create(['name' => 'Customer']);

    $this->actingAs($admin)->post(route('admin.data.store', 'users'), [
        'name' => 'New Customer',
        'username' => 'newcustomer',
        'email' => 'new-customer@example.com',
        'password' => 'password',
    ])->assertRedirect();

    $this->assertDatabaseHas('users', [
        'email' => 'new-customer@example.com',
        'role_id' => $customerRole->id,
    ]);
});

test('admin cannot promote a customer to admin', function () {
    $admin = actingAdmin();
    $customerRole = Role::create(['name' => 'Customer']);
    $customer = User::factory()->create(['role_id' => $customerRole->id]);

    $this->actingAs($admin)->patch(route('admin.data.update', ['users', $customer->id]), [
        'role_id' => $admin->role_id,
        'name' => $customer->name,
        'username' => $customer->username,
        'email' => $customer->email,
    ])->assertStatus(422);

    expect($customer->refresh()->role_id)->toBe($customerRole->id);
});

test('admin account and admin role are protected', function () {
    $admin = actingAdmin();

    $this->actingAs($admin)
        ->delete(route('admin.data.destroy', ['users', $admin->id]))
        ->assertStatus(422);

    $this->actingAs($admin)
        ->delete(route('admin.data.destroy', ['roles', $admin->role_id]))
        ->assertStatus(422);

    $this->assertDatabaseHas('users', ['id' => $admin->id]);
    $this->assertDatabaseHas('roles', ['id' => $admin->role_id, 'name' => 'Admin']);
});
