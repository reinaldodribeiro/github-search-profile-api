<?php


namespace App\Services;


use App\Models\ProfileGitHub;
use App\Traits\GitHubAPI;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ProfileGitHubService extends CrudService
{
    use GitHubAPI;

    protected $userSearchedProfileService;

    public function __construct(UserSearchedProfileService $userSearchedProfileService)
    {
        $this->userSearchedProfileService = $userSearchedProfileService;
    }

    public function list($params = [])
    {
        return $this->getProfilesUser($params);
    }

    public function searchUserProfile($username)
    {
        $user = $this->getModel()->getUserByUserName($username);
        if(!empty($user)) {
            return $user;
        }else{
            $data = $this->searchProfile($username);
            $this->save($data);
            return $this->getModel()->getUserByUserName($username);
        }
    }

    public function getProfilesUser($params = [])
    {
        $query = DB::table('users_searched_profiles as us')
            ->join('profiles_github as pg', 'us.id_profile_github', '=', 'pg.id')
            ->where('us.id_user', auth()->user()->getAuthIdentifier())
            ->select('pg.*');
        if(Arr::has($params, 'is_favorite')){
            $query->where('pg.is_favorite', Arr::get($params, 'is_favorite'));
        }
        return $query->orderBy('pg.created_at', 'DESC')
            ->paginate(intval(Arr::get($params, 'per_page', 10)));
    }

    protected function postSave($model, $data)
    {
        return $this->userSearchedProfileService->save($data);
    }

    protected function getModel($data = [])
    {
        return new ProfileGitHub($data);
    }

}
