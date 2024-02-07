<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions people
        Permission::create(['name' => 'read all people', 'guard_name' => 'api']);
        Permission::create(['name' => 'read one people', 'guard_name' => 'api']);
        Permission::create(['name' => 'create people', 'guard_name' => 'api']);
        Permission::create(['name' => 'edit people', 'guard_name' => 'api']);
        Permission::create(['name' => 'delete peole', 'guard_name' => 'api']);

        // create roles and assign created permissions

        // this can be done as separate statements
        $role = Role::create(['guard_name' => 'api','name' => 'vendedor']);
        $role->givePermissionTo(['read all people', 'read one people']);

        // or may be done by chaining
        $role = Role::create(['name' => 'admin'])
            ->givePermissionTo(['read all people', 'read one people', 'create people', 'edit people']);

        $role = Role::create(['name' => 'super-admin']);
        $role->givePermissionTo(Permission::all());
    }

    private function createPermissionsUsingEloquentsInsert()
    {
        $arrayOfPermissionNames = ['writer', 'editor'];
        $permissions = collect($arrayOfPermissionNames)->map(function ($permission) {
            return ['name' => $permission, 'guard_name' => 'web'];
        });

        Permission::insert($permissions->toArray());
    }

    /**
     * Alternativamente, puede utilizar DB::insert, siempre que también proporcione todos los campos de datos requeridos. A continuación se muestra un ejemplo de esto... pero tenga en cuenta que este ejemplo codifica los nombres de las tablas y los nombres de los campos, por lo que no respeta ninguna personalización que pueda tener en su archivo de configuración de permisos.
     */
    private function createPermissionsByRole()
    {
        $permissionsByRole = [
            'admin' => ['restore posts', 'force delete posts'],
            'editor' => ['create a post', 'update a post', 'delete a post'],
            'viewer' => ['view all posts', 'view a post']
        ];

        $insertPermissions = fn ($role) => collect($permissionsByRole[$role])
            ->map(fn ($name) => DB::table('permissions')->insertGetId(['name' => $name, 'guard_name' => 'web']))
            ->toArray();

        $permissionIdsByRole = [
            'admin' => $insertPermissions('admin'),
            'editor' => $insertPermissions('editor'),
            'viewer' => $insertPermissions('viewer')
        ];

        foreach ($permissionIdsByRole as $role => $permissionIds) {
            $role = Role::whereName($role)->first();

            DB::table('role_has_permissions')
                ->insert(
                    collect($permissionIds)->map(fn ($id) => [
                        'role_id' => $role->id,
                        'permission_id' => $id
                    ])->toArray()
                );
        }

        // and also add the command to flush the cache again now after doing all these inserts
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * PRECAUCIÓN: CADA VEZ QUE EJECUTE CONSULTAS DE BD DIRECTAMENTE, estará omitiendo las funciones de control de caché. Por lo tanto, deberá vaciar manualmente la caché del paquete DESPUÉS de ejecutar consultas directas a la base de datos, incluso en una sembradora.
     */
}
