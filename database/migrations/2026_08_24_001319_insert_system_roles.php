<?php


use App\Enums\RoleDescription;
use App\Enums\RoleName;
use App\Enums\RoleCode;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const string ADMIN_CODE           = RoleCode::Admin->value;
    private const string ADMIN_NAME           = RoleName::Admin->value;
    private const string ADMIN_DESCRIPTION    = RoleDescription::Admin->value;

    private const string MANAGER_CODE         = RoleCode::Manager->value;
    private const string MANAGER_NAME         = RoleName::Manager->value;
    private const string MANAGER_DESCRIPTION  = RoleDescription::Manager->value;

    private const string EMPLOYEE_CODE        = RoleCode::Employee->value;
    private const string EMPLOYEE_NAME        = RoleName::Employee->value;
    private const string EMPLOYEE_DESCRIPTION = RoleDescription::Employee->value;

    private const string USER_CODE            = RoleCode::User->value;
    private const string USER_NAME            = RoleName::User->value;
    private const string USER_DESCRIPTION     = RoleDescription::User->value;


    /**
     * Run the migrations.
     */
    public function up(): void
    {

        DB::table('roles')->upsert(
          [
              [
                  'code'        => self::ADMIN_CODE,
                  'name'        => self::ADMIN_NAME,
                  'description' => self::ADMIN_DESCRIPTION,
                  'is_system_role' => True,

              ],

              [
                  'code'        => self::MANAGER_CODE,
                  'name'        => self::MANAGER_NAME,
                  'description' => self::MANAGER_DESCRIPTION,
                  'is_system_role' => True,

              ],

              [
                  'code'        => self::EMPLOYEE_CODE,
                  'name'        => self::EMPLOYEE_NAME,
                  'description' => self::EMPLOYEE_DESCRIPTION,
                  'is_system_role' => True,

              ],

              [
                  'code'        => self::USER_CODE,
                  'name'        => self::USER_NAME,
                  'description' => self::USER_DESCRIPTION,
                  'is_system_role' => True,

              ],
          ],
          ['code'],
            ['name', 'description'],
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('roles')
                 ->whereIn('code', [self::ADMIN_CODE,self::MANAGER_CODE,
                                    self::EMPLOYEE_CODE, self::USER_CODE,
                ])->delete();
    }
};
