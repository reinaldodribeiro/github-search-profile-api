<?php

namespace App\Services;

use App\models\UserSearchedProfile;
use Illuminate\Support\Arr;

class UserSearchedProfileService extends CrudService
{
    protected function prepareSave($data)
    {
        $finalData = [];
        $finalData["id_profile_github"] = Arr::get($data, 'id');
        $finalData["id_user"] = auth()->user()->getAuthIdentifier();
        return $finalData;
    }

    public function save($data)
    {
        if(!$this->userHasSearched($data['id'])){
            return parent::save($data);
        }
    }

    public function userHasSearched($id)
    {
        return $this->getModel()
            ->where('id_user', auth()->user()->getAuthIdentifier())
            ->where('id_profile_github', $id)->count() > 0;
    }

    protected function getModel($data = [])
    {
        return new UserSearchedProfile($data);
    }
}
