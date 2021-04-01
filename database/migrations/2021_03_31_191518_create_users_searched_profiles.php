<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersSearchedProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_searched_profiles', function (Blueprint $table) {
            $table->char('id_user_searched_profile', 32)->charset('ascii')->primary();
            $table->char('id_user', 32)->charset('ascii');
            $table->bigInteger('id_profile_github');
            $table->foreign('id_user')->references('id_user')->on('users');
            $table->foreign('id_profile_github')->references('id')->on('profiles_github');
            $table->timestamps();
        });

        DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');
        DB::statement("ALTER TABLE users_searched_profiles ALTER COLUMN id_user_searched_profile SET DEFAULT replace(CAST(uuid_generate_v4() as char(36)),'-','');");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_searched_profiles');
    }
}
