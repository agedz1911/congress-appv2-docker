<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Participant extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
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

    protected static function booted(): void
    {
        static::creating(function (self $participant): void {
            if (filled($participant->id)) {
                return;
            }

            $lastId = static::query()->latest('id')->value('id');
            $lastNumber = $lastId ? (int) substr($lastId, 6) : 0;

            $participant->id = 'event-' . str_pad((string) ($lastNumber + 1), 4, '0', STR_PAD_LEFT);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
