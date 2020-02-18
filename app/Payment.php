<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class Payment extends Model {
 public $table = "payments";
    public $timestamps = false;

    protected $fillable = ['transaction_id', 'payment_id', 'api_token'];

    public function user()
    {
     return $this->belongsTo(User::class);
    }
    public function lists()
    {
     return $this->hasMany(Lists::class);
    }
}

