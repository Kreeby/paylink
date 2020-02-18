<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class tempTransaction extends Model {
 public $table = "temp";

    protected $fillable = ['amount', 'name', 'merchant_name', 'code', 'currency', 'transaction_id', 'logo_url', 'generated_code'];

    public function user()
    {
     return $this->belongsTo(User::class);
    }
    public function lists()
    {
     return $this->hasMany(Lists::class);
    }
}

