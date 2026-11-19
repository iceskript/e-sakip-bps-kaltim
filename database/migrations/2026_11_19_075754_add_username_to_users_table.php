<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->nullable();
        });

        $usedUsernames = [];

        foreach (DB::table('users')->select('id', 'email')->orderBy('id')->get() as $user) {
            $base = preg_replace('/[^a-z0-9._-]/', '', strtolower((string) strtok($user->email, '@'))) ?: 'user';
            $base = substr($base, 0, 50);
            $username = $base;
            $suffix = 2;

            while (isset($usedUsernames[$username])) {
                $suffixText = '-'.$suffix;
                $username = substr($base, 0, 50 - strlen($suffixText)).$suffixText;
                $suffix++;
            }

            $usedUsernames[$username] = true;
            DB::table('users')->where('id', $user->id)->update(['username' => $username]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->unique('username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_username_unique');
            $table->dropColumn('username');
        });
    }
};
