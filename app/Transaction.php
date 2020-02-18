<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class Transaction extends Model {
 public $table = "transactions";

    protected $fillable = ['amount', 'name', 'merchant_name', 'code', 'currency', 'transaction_id', 'logo_url'];

    public function user()
    {
     return $this->belongsTo(User::class);
    }
    public function lists()
    {
     return $this->hasMany(Lists::class);
    }
}

