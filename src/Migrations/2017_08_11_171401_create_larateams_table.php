<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLaraTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( \Config::get( 'larateams.users_table' ), function ( Blueprint $table )
        {
            $table->integer( 'current_team_id' )->unsigned()->nullable();
        } );
        Schema::create( \Config::get( 'larateams.teams_table' ), function ( Blueprint $table )
        {
            $table->increments( 'id' )->unsigned();
            $table->integer( 'owner_id' )->unsigned()->nullable();
            $table->string( 'name' );
            $table->timestamps();
        } );
        Schema::create( \Config::get( 'larateams.team_user_table' ), function ( Blueprint $table )
        {
            $table->integer( 'user_id' )->unsigned();
            $table->integer( 'team_id' )->unsigned();
            $table->timestamps();
            $table->foreign( 'user_id' )
                  ->references( \Config::get( 'larateams.user_foreign_key' ) )
                  ->on( \Config::get( 'larateams.users_table' ) )
                  ->onUpdate( 'cascade' )
                  ->onDelete( 'cascade' );
            $table->foreign( 'team_id' )
                  ->references( 'id' )
                  ->on( \Config::get( 'larateams.teams_table' ) )
                  ->onDelete( 'cascade' );
        } );
        Schema::create( \Config::get( 'larateams.team_invites_table' ), function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('team_id')->unsigned();
            $table->enum('type', ['invite', 'request']);
            $table->string('email');
            $table->string('accept_token');
            $table->string('deny_token');
            $table->timestamps();
            $table->foreign( 'team_id' )
                  ->references( 'id' )
                  ->on( \Config::get( 'larateams.teams_table' ) )
                  ->onDelete( 'cascade' );
        });

        Schema::create('team_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('label')->nullable();
            $table->timestamps();
        });

        Schema::table(\Config::get( 'larateams.team_user_table' ), function (Blueprint $table) {
            $table->integer('team_role_id')->unsigned()->default(\Config::get('larateams.default_team_role'));
        });


    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(\Config::get( 'larateams.users_table' ), function(Blueprint $table)
        {
            $table->dropColumn('current_team_id');
        });
        Schema::table(\Config::get('larateams.team_user_table'), function (Blueprint $table) {
            $table->dropForeign(\Config::get('larateams.team_user_table').'_user_id_foreign');
            $table->dropForeign(\Config::get('larateams.team_user_table').'_team_id_foreign');
        });
        Schema::drop(\Config::get('larateams.team_user_table'));
        Schema::drop(\Config::get('larateams.team_invites_table'));
        Schema::drop(\Config::get('larateams.teams_table'));

        Schema::table(\Config::get( 'larateams.team_user_table' ), function ($table) {
            $table->dropColumn('team_role_id');
        });
        Schema::dropIfExists('team_roles');
    }
}
