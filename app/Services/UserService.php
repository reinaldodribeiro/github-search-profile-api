<?php

namespace App\Services;

use App\Rules\SometimesUnless;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService extends CrudService
{

    protected function getRules($data, $saving, $model)
    {
        $rules = [
            'email' => [
                new SometimesUnless($saving, $data, 'email'),
                'string',
                'email',
                'max:50',
                'max:255',
                'unique:users'
            ],
            'password' => [
                new SometimesUnless($saving, $data, 'password'),
                'string',
                'min:8',
                'confirmed'
            ]
        ];

        return $rules;
    }

    protected function prepareSave($data)
    {
        $finalData = parent::prepareSave($data);
        $finalData['password'] = Hash::make($data['password']);
        return $finalData;
    }

    protected function postSave($model, $data)
    {
        return $model;
    }


    protected function getModel($data = [])
    {
        return new User($data);
    }


}
