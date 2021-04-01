<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileGitHub extends Model
{
    protected $table = "profiles_github";

    protected $primaryKey = "id";

    protected $fillable = ["id", "login", "avatar_url", "is_favorite"];


    public function getUserByUserName($username)
    {
        return $this->where('login', $username)->first();
    }
}
