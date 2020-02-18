<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class Access extends Model {
 public $table = "access_codes";

    protected $fillable = ['access_code', 'device_ID'];

    public function user()
    {
     return $this->belongsTo(User::class);
    }
    public function lists()
    {
     return $this->hasMany(Lists::class);
    }
}

