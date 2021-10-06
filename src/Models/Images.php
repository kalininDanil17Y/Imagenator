<?php
namespace Imagenator\Model;
use Illuminate\Database\Eloquent\Model as Eloquent;

class g extends Eloquent
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['uuid', 'name', 'ipAddress'];
}