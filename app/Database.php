<?php
namespace App\Imagenator;
use Illuminate\Database\Capsule\Manager as Capsule;

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
    }
}