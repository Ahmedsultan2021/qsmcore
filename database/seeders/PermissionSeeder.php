<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Employee Management
            'employees.view',
            'employees.create',
            'employees.edit',
            'employees.delete',
            
            // Reports Management
            'reports.view',
            'reports.create',
            'reports.edit',
            'reports.delete',
            
            // Incidents Management
            'incidents.view',
            'incidents.create',
            'incidents.edit',
            'incidents.delete',
            
            // Corrective Actions
            'corrective-actions.view',
            'corrective-actions.create',
            'corrective-actions.edit',
            'corrective-actions.delete',
            
            // Quality Management
            'quality.view',
            'quality.create',
            'quality.edit',
            'quality.delete',
            
            // Safety Management
            'safety.view',
            'safety.create',
            'safety.edit',
            'safety.delete',
            
            // Roles & Permissions (for companies only)
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'roles.assign',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'employee']);
        }

        // Create roles
        $roles = [
            'admin' => [
                'employees.view',
                'employees.create',
                'employees.edit',
                'employees.delete',
                'reports.view',
                'reports.create',
                'reports.edit',
                'reports.delete',
                'incidents.view',
                'incidents.create',
                'incidents.edit',
                'incidents.delete',
                'corrective-actions.view',
                'corrective-actions.create',
                'corrective-actions.edit',
                'corrective-actions.delete',
                'quality.view',
                'quality.create',
                'quality.edit',
                'quality.delete',
                'safety.view',
                'safety.create',
                'safety.edit',
                'safety.delete',
                'roles.view',
                'roles.create',
                'roles.edit',
                'roles.delete',
                'roles.assign',
            ],
            'manager' => [
                'employees.view',
                'employees.create',
                'employees.edit',
                'reports.view',
                'reports.create',
                'reports.edit',
                'incidents.view',
                'incidents.create',
                'incidents.edit',
                'corrective-actions.view',
                'corrective-actions.create',
                'corrective-actions.edit',
                'quality.view',
                'quality.create',
                'quality.edit',
                'safety.view',
                'safety.create',
                'safety.edit',
            ],
            'supervisor' => [
                'employees.view',
                'reports.view',
                'reports.create',
                'incidents.view',
                'incidents.create',
                'corrective-actions.view',
                'corrective-actions.create',
                'quality.view',
                'quality.create',
                'safety.view',
                'safety.create',
            ],
            'employee' => [
                'reports.view',
                'reports.create',
                'incidents.view',
                'incidents.create',
                'quality.view',
                'safety.view',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::create(['name' => $roleName, 'guard_name' => 'employee']);
            $role->givePermissionTo($rolePermissions);
        }
    }
}
