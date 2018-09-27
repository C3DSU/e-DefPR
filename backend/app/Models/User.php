<?php

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Services\Mailer;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\View;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;

    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cpf',
        'birth_date',
        'birthplace',
        'rg',
        'rg_issuer',
        'gender',
        'marital_status',
        'profession',
        'note',
        'birthplace',
        'addresses',
        'must_change_password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Generate hash for the password attribute
     *
     * @param $value
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Generate json for the addresses attribute
     *
     * @param $value
     */
    public function setAddressesAttribute($value)
    {
        $this->attributes['addresses'] = json_encode($value);
    }

    /**
     * Get all of the attendments for the user.
     */
    public function attendments()
    {
        return $this->hasMany(Attendment::class, 'user_id');
    }

    /**
     * @throws \Exception
     */
    public function resetPassword()
    {
        $temporaryPassword = uniqid(time());
        $hashedPassword = bcrypt($temporaryPassword);
        $this->password = $hashedPassword;
        $this->must_change_password = true;
        $this->save();

        $address = [
            'email' => $this->email,
            'name' => $this->name
        ];

        $htmlProvider = View::make('templates/resetPassword', [
            'user' => $this,
            'temporaryPassword' => $temporaryPassword
        ]);

        $html = $htmlProvider->render();

        Mailer::sendEmail([ $address ], 'Recuperação de Senha', $html);
    }
}
