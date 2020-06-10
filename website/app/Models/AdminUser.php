<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
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
 *     required={"first_name","last_name","email","password"},
 *     @OA\Xml(
 *         name="AdminUser"
 *     ),
 * )
 */
class AdminUser extends Authenticatable implements JWTSubject
{
    use softDeletes;
    use Notifiable;
    
    private $guard = 'admin';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'admin_user_id';
    
    /**
     * @OA\Property(),
     * @var integer
     */
    private $admin_user_id;

    /**
     * @OA\Property()
     * @var boolean
     */
    private $is_activated;

    /**
     * @OA\Property()
     * @var boolean
     */
    protected $softDelete = true;

    /**
     * @OA\Property(
     *     example="2017-02-02 18:31:45",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    protected $deleted_at;

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
    private $first_name;

    /**
     * @OA\Property(),
     * @var string
     */
    private $last_name;

    /**
     * @OA\Property(),
     * @var string
     */
    private $email;

    /**
     * @OA\Property(),
     * @var integer
     */
    private $client_id;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'is_activated',
        'updated_at',
        'first_name',
        'last_name',
        'is_superadmin',
        'email',
        'password',
        'client_id'
    ];

    /**
     * The dates attributes
     *
     * @var array
     */
    protected $dates = [
		'deleted_at',
        'updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'deleted_at'
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

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }
}
