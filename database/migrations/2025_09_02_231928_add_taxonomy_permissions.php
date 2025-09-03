<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create Taxonomy permissions
        $taxonomyActions = ['List', 'Create', 'Read', 'Update', 'Delete'];
        foreach ($taxonomyActions as $action) {
            Permission::firstOrCreate([
                'name' => "{$action} Taxonomy",
                'category' => 'Taxonomy',
            ]);
        }

        // Get the roles
        $regular = Role::where('name', 'Regular User')->first();
        $superAdmin = Role::where('name', 'Super Admin')->first();
        $securityAdmin = Role::where('name', 'Security Admin')->first();
        $internalAuditor = Role::where('name', 'Internal Auditor')->first();

        // Get the permissions
        $taxonomyPermissions = Permission::where('category', 'Taxonomy')->get();

        // Assign permissions to Super Admin (all permissions)
        if ($superAdmin) {
            $superAdmin->givePermissionTo($taxonomyPermissions);
        }

        // Assign permissions to Regular User (List and Read only)
        if ($regular) {
            $regular->givePermissionTo([
                'List Taxonomy',
                'Read Taxonomy',
            ]);
        }

        // Assign permissions to Security Admin (List, Create, Read, Update - no Delete)
        if ($securityAdmin) {
            $securityAdmin->givePermissionTo([
                'List Taxonomy',
                'Create Taxonomy',
                'Read Taxonomy',
                'Update Taxonomy',
            ]);
        }

        // Assign permissions to Internal Auditor (List and Read only)
        if ($internalAuditor) {
            $internalAuditor->givePermissionTo([
                'List Taxonomy',
                'Read Taxonomy',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove Taxonomy permissions
        $taxonomyActions = ['List', 'Create', 'Read', 'Update', 'Delete'];
        foreach ($taxonomyActions as $action) {
            Permission::where('name', "{$action} Taxonomy")->delete();
        }
    }
};
