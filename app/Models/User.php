<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function projectUsers(): HasMany
    {
        return $this->hasMany(ProjectUser::class);
    }

    // Projects this user is assigned to as annotator
    public function assignedProjects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_users')
            ->wherePivot('role', 'annotator')
            ->withPivot('role', 'joined_at')
            ->withTimestamps()
            ->withCasts(['joined_at' => 'datetime']);
    }


    public function ownedProjects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function annotations(): HasMany
    {
        return $this->hasMany(Annotation::class);
    }

    public function rowAssignments(): HasMany
    {
        return $this->hasMany(RowAssignment::class, 'user_id');
    }
}
