<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;
    use BelongsToTenant;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function children(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'parent_student', 'parent_id', 'student_id')->withTimestamps();
    }

    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'parent_student', 'student_id', 'parent_id')->withTimestamps();
    }
}
