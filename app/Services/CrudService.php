<?php

namespace App\Services;

use App\Exceptions\RulesValidation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

abstract class CrudService
{
    /**
     * Listagem Geral da Entidade
     *
     * @param
     * @return
     */
    public function list($params = [])
    {
        $model = $this->prepareModel();
        $page = isset($params['page']) ? $params['page'] : 1;
        $items_per_page = isset($params['items_per_page']) ? $params['items_per_page'] : $this->perPage($model);
        $items = $this->prepareList($this->applyFilters($model, $params), $params)->paginate($items_per_page, ['*'], 'page', $page);
        foreach ($items as $model){
            $this->prepareItem($model);
        }
        return $items->toArray();
    }

    /**
     * Listar por id
     *
     * @param array $params
     * @param string $id
     * @return
     */
    public function search($params = [], $id)
    {
        $model = $this->getModel();
        return $this->prepareItem($this->applyFilters($model, $params)->findOrFail($id));
    }

    /**
     * @param array $data
     * @throws ValidationException
     * @return array
     */
    public function save($data) {
        $this->getValidator($data, true, null)->validate();
        return $this->performSave($data);
    }

    /**
     * @param string $id
     * @param array $data
     * @return array
     */
    public function update($id, $data) {
        $model = $this->getModel()->findOrFail($id);
        $data = $this->prepareDataWithQuery($model, $data);
        $this->getValidator($data, false, $model)->validate();
        $this->getValidatorOtherRules($data, false,  $model);
        return $this->performUpdate($model, $data);
    }

    /**
     *
     * @param array $data
     * @param string $id
     * @return array
     */
    public function delete($data, $id) {

        $model = $this->getModel()->findOrFail($id);

        if(!Arr::has($data, 'is_delete')){
            $errors = [
                "is_delete" => ["Informe o atributo is_delete para realizar a requisição"]
            ];
            throw new RulesValidation($errors);
        }

        if($data['is_delete'] == false || $data['is_delete'] == "false"){
            $data['deleted_at'] = now();
        }

        $this->getValidatorOtherRules($data,false ,$model, $id);
        return $this->performDelete($model, $data);
    }


    /**
     * @param Model $model
     * @param array $data
     * @param array $responseData
     * @return array
     */
    protected function performUpdate($model, $data) {
        return DB::transaction(function () use ($model, $data) {
            $this->updateBefore($model, $data);
            $model->update($this->prepareUpdate($model, $data));
            $this->updateAfter($model, $data);
            return $this->postUpdate($model, $data);
        });
    }

    /**
     * @param Model $model
     * @param array $data
     * @param array $responseData
     * @return array
     */
    protected function performDelete($model, $data) {
        return DB::transaction(function () use ($model, $data) {
            $is_delete = Arr::get($data, 'is_delete');
            $this->destroyBefore($model, $data);
            if($is_delete === true || $is_delete === 'true'){
                $model->delete();
            }else{
                $model->update($this->prepareUpdate($model, $data));
            }
            return $this->postDelete($model, $data);
        });
    }

    /**
     * @param array $data
     * @param boolean $saving
     * @param Model $model
     * @return mixed
     */
    protected function getValidator($data, $saving, $model) {
        return Validator::make($data, $this->getRules($data, $saving, $model), $this->getCustomAttributes());
    }

    protected function getRules($data, $saving, $model) {
        return [];
    }

    protected function getValidatorOtherRules($data, $saving, $model, $id = null){
        $rules = $this->getOthersRules($data, $saving, $model);
        if(!empty($rules)){
            throw new RulesValidation($rules);
        }
    }

    protected function getOthersRules($data, $saving , $model){
        return [];
    }

    protected function getCustomAttributes() {
        return [];
    }

    /**
     * @param array $data
     * @param array $responseData
     * @return array
     */
    protected function performSave($data) {
        return DB::transaction(function () use ($data) {
            $this->saveBefore($data);
            $model = $this->getModel($this->prepareSave($data));
            $model->save();
            $this->saveAfter($model, $data);
            return $this->postSave($model, $data);
        });
    }


    /**
     * @param Model $model
     * @param array $params
     * @return Model
     */
    private function applyFilters($model, $params)
    {
        $filters = $this->prepareFilters($model, $params);
        $query = $model->where($filters['and'])->where(function ($query) use ($filters) {
            foreach ($filters['or'] as $condition) {
                $query->orWhere($condition[0], $condition[1], $condition[2]);
            }
        });
        if(isset($filters['in'][0])){
            foreach ($filters['in'] as $key => $filterIn){
                $query->whereIn($filters['in'][$key][0], $filters['in'][$key][1]);
            }
        }
        return $query;
    }


    /**
     * @param Model $model
     * @param array $params
     * @return array
     */
    protected function prepareFilters($model, $params) {

        $queryFilters = isset($model->queryFilters) ? $model->queryFilters : [];
        $finalParams = [
            'or' => [],
            'and' => [],
            'in' => []
        ];
        if (count($queryFilters)) {
            foreach ($params as $key => $value) {
                if (isset($queryFilters['or']) && array_key_exists($key, $queryFilters['or'])) {
                    $finalParams['or'][] = [
                        $key,
                        $queryFilters['or'][$key],
                        $value
                    ];
                } else if (isset($queryFilters['and']) && array_key_exists($key, $queryFilters['and'])) {
                    $finalParams['and'][] = [
                        $key,
                        $queryFilters['and'][$key],
                        $value
                    ];
                } else if (isset($queryFilters['in']) && array_key_exists($key, $queryFilters['in'])) {
                    $arrayFilter = [];
                    $explode_val = explode(',', $value);

                    foreach ($explode_val as $itemValue){
                        if( $queryFilters['in'][$key] == "int"){
                            array_push($arrayFilter, intval($itemValue));
                        }else{
                            array_push($arrayFilter, $itemValue);
                        }
                    }
                    $finalParams['in'][] = [
                        $key,
                        $arrayFilter
                    ];
                }
            }
            foreach ($finalParams['and'] as $key => $value){
                if($value[1] == "like"){
                    $finalParams['and'][$key][2] = "%".$value[2]."%";
                }
            }
            foreach ($finalParams['or'] as $key => $value){
                if($value[1] == "like"){
                    $finalParams['or'][$key][2] = "%".$value[2]."%";
                }
            }
        }
        return $finalParams;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function prepareSave($data)
    {
        return $this->prepareFromFillable($data);
    }

    /**
     * @param Model $model
     * @param array $data
     * @return array
     */
    protected function prepareUpdate($model, $data) {
        $finalData = $this->prepareFromFillable($data);
        $guard = isset($model->guardFromUpdate) ? $model->guardFromUpdate : [];
        foreach ($guard as $key) {
            unset($finalData[$key]);
        }
        return $finalData;
    }


    /**
     * @param  array $data
     * */
    protected function saveBefore($data)
    {
        return true;
    }

    /**
     * @param  array $data
     * */
    protected function updateBefore($model, $data)
    {
        return true;
    }

    /**
     * @param  array $data
     * */
    protected function saveAfter($model, $data)
    {
        return true;
    }

    /**
     * @param  array $data
     * */
    protected function updateAfter($model, $data)
    {
        return true;
    }

    /**
     * @param  array $data
     * */
    protected function destroyBefore($model, $data)
    {
        return true;
    }

    /**
     * @param Model $model
     * @param array $data
     * @return array
     */
    protected function postSave($model, $data) {
        return [];
    }

    /**
     * @param Model $model
     * @param array $data
     * @return array
     */
    protected function postUpdate($model, $data) {
        return [];
    }

    /**
     * @param Model $model
     * @param array $data
     * @return array
     */
    protected function postDelete($model, $data) {
        return [];
    }


    /**
     * @param array $data
     * @param array $responseData
     * @return array
     */
    protected function prepareFromFillable($data) {
        $fillable = $this->getModel()->getFillable();
        $finalData = [];
        if (isset($fillable, $data)) {
            foreach ($fillable as $column) {
                if (array_key_exists($column, $data)) {
                    $finalData[$column] = $data[$column];
                }
            }
        }
        return $finalData;
    }


    /**
     * @return int
     */
    protected function perPage($model)
    {
        return isset($model->perPage) ? $model->perPage : 10;
    }

    /**
     * @param Model $model
     * @return Model $model
     */
    protected function prepareItem($model)
    {
        return $model;
    }

    /**
     * @param Model $model
     * @param array $data
     * @return array
     */
    protected function prepareDataWithQuery($model, $data)
    {
        return $data;
    }

    protected function prepareQuerySelect($model)
    {
        $finalData = array();
        foreach ($model->querySelect as $userDono => $key){
            $finalData[$key] = $model->$key;
        }
        return $finalData;
    }

    /**
     * @param Builder $list
     * @param array $params
     * @return Builder
     */
    protected function prepareList($list, $params)
    {
        return $list;
    }

    /**
     * Retorna o Model com a instancia desejada.
     * @return Model
     */
    protected function prepareModel($data = [])
    {
        return $this->getModel();
    }

    /**
     * Retorna o Model com a instancia desejada.
     * @return Model
     */
    protected abstract function getModel($data = []);


}
