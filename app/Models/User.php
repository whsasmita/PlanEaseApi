<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'id_user';

    protected $fillable = [
        'full_name',
        'email',
        'email_verified_at',
        'phone',
        'password',
        'role',
    ];

    protected $hidden = ['password', 'remember_token'];

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

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role,
        ];
    }

    /**
     * Check if the user has a specific role.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id', 'id_user');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'user_id', 'id_user');
    }

    // public function notifications()
    // {
    //     return $this->hasMany(Notification::class, 'user_id', 'id_user');
    // }

    public function pollings()
    {
        return $this->hasMany(Polling::class, 'user_id', 'id_user');
    }

}
