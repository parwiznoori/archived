<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class GrantActivityReportToQcRole extends Migration
{
    /**
     * کاربران کنترول‌کننده (رول quality_Control) باید بتوانند راپور فعالیت کاربران آرشیف را ببینند.
     *
     * @return void
     */
    public function up()
    {
        $permissionId = DB::table('permissions')
            ->where('name', 'archiveuser_activity_report')
            ->whereNull('deleted_at')
            ->value('id');

        if (!$permissionId) {
            return;
        }

        $roleIds = DB::table('roles')
            ->where('name', 'quality_Control')
            ->whereNull('deleted_at')
            ->pluck('id');

        foreach ($roleIds as $roleId) {
            $exists = DB::table('role_has_permissions')
                ->where('permission_id', $permissionId)
                ->where('role_id', $roleId)
                ->exists();

            if (!$exists) {
                DB::table('role_has_permissions')->insert([
                    'permission_id' => $permissionId,
                    'role_id' => $roleId,
                ]);
            }
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $permissionIds = DB::table('permissions')
            ->where('name', 'archiveuser_activity_report')
            ->whereNull('deleted_at')
            ->pluck('id');

        $roleIds = DB::table('roles')
            ->where('name', 'quality_Control')
            ->whereNull('deleted_at')
            ->pluck('id');

        DB::table('role_has_permissions')
            ->whereIn('permission_id', $permissionIds)
            ->whereIn('role_id', $roleIds)
            ->delete();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
