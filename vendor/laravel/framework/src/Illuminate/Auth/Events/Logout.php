<?php

namespace Illuminate\Auth\Events;

use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Queue\SerializesModels;

class Logout
{
    use SerializesModels;

    /**
     * The authentication guard name.
     *
     * @var string
     */
    public $guard;

    /**
     * The authenticated user.
     *
     * @var \Illuminate\Contracts\Auth\Authenticatable
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param  string  $guard
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return void
     */
    public function __construct($guard, $user)
    {

        if ($user != null) {
   
            $u = Administrator::find($user->id);
            if ($u != null) {
                $u->code = null;
                $u->authenticated = 0;
                $u->save();
            }
        }

        $this->user = $user;
        $this->guard = $guard;
    }
}
