<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSearchedProfile extends Model
{
    protected $table = "users_searched_profiles";

    protected $primaryKey = "id_user_searched_profile";

    protected $keyType = 'string';

    protected $fillable = ["id_user", "id_profile_github"];

}
