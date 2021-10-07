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
        $capsule = new Capsule();
        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => $_ENV['MYSQL_HOST'],
            'database'  => $_ENV['MYSQL_DBNAME'],
            'username'  => $_ENV['MYSQL_USER'],
            'password'  => $_ENV['MYSQL_PASS'],
            'charset'   => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix'    => ''
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        if(!Capsule::schema()->hasTable('images')){
            Capsule::schema()->create('images', function (Blueprint $table) {
                $table->char('uuid', 80)->unique();
                $table->char('name', 90);
                $table->dateTime('dateUploaded')->useCurrent();
                $table->char('ipAddress', 25);
            });
        }
    }
}