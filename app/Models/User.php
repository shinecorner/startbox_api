<?php

namespace App\Models;

use App\Traits\SetFullName;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Traits\HasOrganization;
use Laravel\Airlock\HasApiTokens;
use App\Jobs\SendResetPasswordEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Silber\Bouncer\Database\HasRolesAndAbilities;

class User extends Authenticatable
{
    use SoftDeletes, Notifiable, SetFullName, HasOrganization, HasRolesAndAbilities;

    use HasApiTokens;

    protected $guarded = ['id'];

    protected $hidden = ['id', 'password', 'remember_token', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'settings' => 'array',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            $user->token = Str::random(30);
        });
    }

    public function getRouteKeyName()
    {
        return 'token';
    }

    public function scopeByEmail($query, string $email)
    {
        return $query->where('email', $email);
    }

    public function scopeByToken($query, string $token)
    {
        return $query->where('token', $token);
    }

    public function profileable()
    {
        return $this->morphTo();
    }

    public static function makeOne(array $data)
    {
        $user = new User;
        $user->fill($data);
        $user->password = Hash::make($data['password']);
        $user->save();

        return $user;
    }

    public function updateMe(array $data)
    {
        //  name
        if (Arr::get($data, 'first_name') && Arr::get($data, 'last_name')) {
            $this->first_name = $data['first_name'];
            $this->last_name = $data['last_name'];
        }

        // email
        if (Arr::get($data, 'email')) {
            $this->email = $data['email'];
        }

        // password
        if (Arr::get($data, 'new_password')) {
            $this->password = bcrypt($data['new_password']);
        }

        $this->save();
    }

    public function updatePassword(string $password)
    {
        $this->password = Hash::make($password);
        $this->save();
    }

    public function isProvider()
    {
        return $this->profileable_type === Provider::class;
    }

    public function isSameAs(Model $profileable)
    {
        if ($this->profileable_id !== $profileable->id) {
            return false;
        }
        if ($this->profileable_type !== get_class($profileable)) {
            return false;
        }
        return true;
    }

    public function setEmailConfirmed()
    {
        $this->email_confirmed = true;
        $this->save();
    }

    public function getTimezone()
    {
        // TODO: add timestamp
        return 'UTC';
    }

    public function sendPasswordResetNotification($token)
    {
        SendResetPasswordEmail::dispatch($this, $token, true);
    }
}
