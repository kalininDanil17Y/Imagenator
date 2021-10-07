<?php
namespace App\Imagenator;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

/**
 * Class Database
 * @package App\Imagenator
 */
class Database
{
    /**
     * Database constructor.
     */
    public function __construct()
    {
        $capsule = new Capsule;
        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'Imagenator',
            'username'  => 'root',
            'password'  => 'pass',
            'charset'   => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix'    => ''
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        if(!Capsule::schema()->hasTable('Images')){
            Capsule::schema()->create('Images', function (Blueprint $table) {
                $table->char('uuid', 80)->unique();
                $table->char('name', 90);
                $table->dateTime('dateUploaded')->useCurrent();
                $table->char('ipAddress', 25);
            });
        }
    }
}