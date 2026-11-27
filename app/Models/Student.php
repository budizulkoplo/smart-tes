<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Student extends Authenticatable
{
    use HasFactory, SoftDeletes;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'classroom_id',
        'nisn',
        'nik',
        'name',
        'password',
        'gender',
        'no_hp',
        'alamat'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * classroom
     *
     * @return void
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
}
