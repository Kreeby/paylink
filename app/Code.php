<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class Code extends Model {
 public $table = "codes";

    protected $fillable = ['generated_code', 'api_token', 'player_id'];

    public function user()
    {
     return $this->belongsTo(User::class);
    }
    public function lists()
    {
     return $this->hasMany(Lists::class);
    }
}

