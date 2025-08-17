<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FileEntry extends Model {
    protected $table = 'files';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'uuid','s3_key','original_name','mime','format','size','width','height','color',
        'is_banned','upload_token_id','tags'
    ];
    protected $casts = ['tags' => 'array', 'is_banned'=>'boolean'];
}
