<?php

namespace Encore\Admin\Auth\Database;

use App\Models\CaseModel;
use App\Models\ConservationArea;
use App\Models\PA;
use App\Models\StudentHasClass;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Class Administrator.
 *
 * @property Role[] $roles
 */
class Administrator extends Model implements AuthenticatableContract, JWTSubject
{
    use Authenticatable;
    use HasPermissions;
    use DefaultDatetimeFormat;
    use Notifiable;

    protected $fillable = ['username', 'password', 'name', 'avatar'];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->name = "{$model->name} {$model->middle_name} {$model->last_name}";

            if ($model->username == null) {
                $model->username = $model->email;
            }

            if ($model->pa_id == null) {
                $model->pa_id = 1;
            }

            $ca = PA::find($model->pa_id);
            if ($ca != null) {
                $model->ca_id = $ca->ca_id;
            } else {
                $model->ca_id = 1;
            }

            return $model;
        });

        self::created(function ($model) {
            //created
        });

        self::updating(function ($model) {
            if ($model->username == null) {
                $model->username = $model->email;
            }

            if ($model->username != $model->email) {
                $model->username = $model->email;
            }

            $model->name = "{$model->name} {$model->middle_name} {$model->last_name}";



            if ($model->pa_id == null) {
                $model->pa_id = 1;
            }

            $ca = PA::find($model->pa_id);
            if ($ca != null) {
                $model->ca_id = $ca->ca_id;
            } else {
                $model->ca_id = 1;
            }

            return $model;
        });

        self::updated(function ($model) {
            // ... code here
        });

        self::deleting(function ($model) {
            // ... code here
        });

        self::deleted(function ($model) {
            // ... code here
        });
    }


    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('admin.database.users_table'));

        parent::__construct($attributes);
    }

    /**
     * Get avatar attribute.
     *
     * @param string $avatar
     *
     * @return string
     */
    public function getNameAttribute($val)
    {
        $n = $this->first_name . " " . $this->middle_name . " " . $this->last_name . " ";
        if (strlen((trim($n))) < 2) {
            return $val;
        }
        return (trim($n));
    }
    public function getAvatarAttribute($avatar)
    {
        if ($avatar == null || strlen($avatar) < 3) {
            $default = url('assets/logo.png');
            return $default;
        }
        $avatar = str_replace('images/', '', $avatar);
        $link = 'storage/images/' . $avatar;

        if (!file_exists(public_path($link))) {
            //dd($avatar);
            $link = 'assets/logo.png';
        }
        return url($link);
    }

    /**
     * A user has and belongs to many roles.
     *
     * @return BelongsToMany
     */
    public function cases()
    {
        return $this->hasMany(CaseModel::class, 'reported_by');
    }
    public function roles(): BelongsToMany
    {
        $pivotTable = config('admin.database.role_users_table');

        $relatedModel = config('admin.database.roles_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'role_id');
    }


    /**
     * A User has and belongs to many permissions.
     *
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        $pivotTable = config('admin.database.user_permissions_table');

        $relatedModel = config('admin.database.permissions_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'permission_id');
    }

    // Rest omitted for brevity

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
        return [];
    }


    public function send2FCode()
    {


        $email = $this->email;

        if ($email == null || strlen($email) < 3) {
            $email = $this->username;
        }

        $this->code = rand(10000000, 99999999);
        $this->save();

        try {
            Mail::send('email_2f_view', ['u' => $this], function ($m) use ($email) {
                $m->to($email, $this->name)
                    ->subject('UWA Offenders database - 2 factor authentication');
                $m->from('noreply@8technologies.cloud', 'UWA Offenders database');
            });
        } catch (\Throwable $th) {
            $msg = 'failed';
            throw $th;
        }
    }

    public function sendPasswordResetCode()
    {
        $email = $this->email;

        if ($email == null || strlen($email) < 3) {
            $email = $this->username;
        }

        $this->code = rand(10000, 99999);
        $this->save();

        try {

            Mail::send('email_view', ['u' => $this], function ($m) use ($email) {
                $m->to($email, $this->name)
                    ->subject('UWA Offenders database - Password reset');
                $m->from('noreply@8technologies.cloud', 'UWA Offenders database');
            });
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    function pa()
    {
        return $this->belongsTo(PA::class, 'pa_id');
    }

    function ca()
    {
        return $this->belongsTo(ConservationArea::class, 'ca_id');
    }
}
