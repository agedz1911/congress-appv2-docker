<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Participant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id_participant',
        'user_id',
        'firstname',
        'lastname',
        'email',
        'country',
        'nik',
        'title',
        'title_of_specialist',
        'type',
        'name_on_certificate',
        'institution',
        'phone',
        'address',
        'city',
        'province',
        'postal_code',
        'roles',
    ];

    protected $casts = [
        'roles' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
