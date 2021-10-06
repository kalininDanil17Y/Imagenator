<?php
namespace Imagenator\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Images extends Eloquent
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['uuid', 'name', 'ipAddress'];
}