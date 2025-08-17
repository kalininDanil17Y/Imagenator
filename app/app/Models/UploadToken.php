<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class UploadToken extends Model {
    protected $fillable = ['name','token','active'];
    protected $hidden = ['token'];
}
