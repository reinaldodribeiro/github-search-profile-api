<?php

namespace App\Http\Controllers\GitHub;

use App\Http\Controllers\CrudController;
use App\Services\ProfileGitHubService;

class ProfileGitHubController extends CrudController
{
    public function __construct(ProfileGitHubService $service)
    {
        $this->service = $service;
    }

    public function searchProfile($username)
    {
        return response()->json(array($this->service->searchUserProfile($username)));
    }


}
