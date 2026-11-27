<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Exam extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'lesson_id',
        'classroom_id',
        'semester',
        'duration',
        'description',
        'random_question',
        'random_answer',
        'show_answer',
    ];

    /**
     * lesson
     *
     * @return void
     */
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * classroom
     *
     * @return void
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * questions
     *
     * @return void
     */
    public function questions()
    {
        return $this->hasMany(Question::class)
            ->orderByRaw('COALESCE(sort_order, id) ASC')
            ->orderBy('id', 'ASC');
    }

    public function scopeAccessibleBy(Builder $query, ?User $user): Builder
    {
        return $query;
    }
}
