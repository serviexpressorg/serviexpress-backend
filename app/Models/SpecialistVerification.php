<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialistVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'criminal_record_file_url',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
