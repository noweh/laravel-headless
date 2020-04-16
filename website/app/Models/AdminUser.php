<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Class AdminUser
 * @package App\Models
 *
 * @OA\Schema(
 *     description="AdminUser model",
 *     title="AdminUser model",
 *     required={"name","email","password"},
 *     @OA\Xml(
 *         name="AdminUser"
 *     ),
 * )
 */
class AdminUser extends Authenticatable implements JWTSubject
{
    use Notifiable;
    
    private $guard = 'admin';
    
    /**
     * @OA\Property(),
     * @var integer
     */
    private $id;

    /**
     * @OA\Property(
     *     example="2017-02-02 18:31:45",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    private $created_at;

    /**
     * @OA\Property(
     *     example="2017-02-02 18:31:45",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    private $updated_at;

    /**
     * @OA\Property()
     * @var boolean
     */
    private $is_superadmin;

    /**
     * @OA\Property(),
     * @var string
     */
    private $name;

    /**
     * @OA\Property(),
     * @var string
     */
    private $email;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'updated_at',
        'name',
        'is_superadmin',
        'email',
        'password'
    ];

    /**
     * The dates attributes
     *
     * @var array
     */
    protected $dates = [
        'updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];
    
    public function getTranslatedAttributes()
    {
        return [];
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
            'origin' => 'admin'
        ];
    }
}
