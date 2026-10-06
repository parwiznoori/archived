<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddReportPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $guard = 'user';

        $permissions = [
            ['name' => 'archivebook_report', 'title' => 'گزارش آمار و احصائیه کتاب'],
            ['name' => 'archiveuser_activity_report', 'title' => 'گزارش فعالیت کاربران آرشیف'],
        ];

        foreach ($permissions as $permission) {
            $exists = DB::table('permissions')
                ->where('name', $permission['name'])
                ->where('guard_name', $guard)
                ->whereNull('deleted_at')
                ->exists();

            if (!$exists) {
                DB::table('permissions')->insert([
                    'guard_name' => $guard,
                    'name' => $permission['name'],
                    'title' => $permission['title'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // واگذاری مجوزها به همان رول‌هایی که view-archive یا archive_report دارند
        $sourceRoleIds = DB::table('role_has_permissions')
            ->whereIn('permission_id', function ($query) {
                $query->select('id')
                    ->from('permissions')
                    ->whereIn('name', ['view-archive', 'archive_report'])
                    ->whereNull('deleted_at');
            })
            ->distinct()
            ->pluck('role_id');

        $newPermissionIds = DB::table('permissions')
            ->whereIn('name', ['archivebook_report', 'archiveuser_activity_report'])
            ->whereNull('deleted_at')
            ->pluck('id');

        foreach ($newPermissionIds as $permissionId) {
            foreach ($sourceRoleIds as $roleId) {
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
            ->whereIn('name', ['archivebook_report', 'archiveuser_activity_report'])
            ->whereNull('deleted_at')
            ->pluck('id');

        DB::table('role_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('name', ['archivebook_report', 'archiveuser_activity_report'])->delete();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
