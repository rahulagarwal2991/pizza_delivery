<?php

namespace App\Repositories;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class EloquentBaseRepository.
 *
 * @author  Rahul
 */
abstract class BaseRepository
{
    /**
     *
     */
    const ID = 'id';
    /**
     *
     */
    const SORT_ASC = 'asc';
    /**
     *
     */
    const SORT_DESC = 'desc';
    /**
     *
     */
    const PAGINATE_ITEM = 10;
    /**
     * @var Model
     */
    private $model;

    /**
     * @var Model
     */
    private $queryBuilder;

    /**
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->setModel($model);
        $this->setQueryBuilder($this->getModel());
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param Model $model
     *
     * @return $this
     *
     * @author Rahul
     */
    protected function setModel(Model $model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @param array $conditions
     *
     * @return mixed
     *
     * @author Rahul
     */
    public function fetchOne(array $conditions = [])
    {
        $result = $this->getQueryBuilder()->where($conditions)->first();
        $this->resetQueryBuilder();

        return $result;
    }

    /**
     * @param array $conditions
     *
     * @return mixed
     *
     * @author Rahul
     */
    public function fetch(array $conditions = [])
    {
        $result = $this->getQueryBuilder()->where($conditions)->get();
        $this->resetQueryBuilder();

        return $result;
    }

    /**
     * @param array $parameters
     *
     * @return static
     *
     * @author Rahul
     */
    public function create(array $parameters)
    {
        return $this->getModel()->create($parameters);
    }

    /**
     * @param array $items
     *
     * @return static
     *
     * @author Rahul
     */
    public function createMultiple(array $items)
    {
        return $this->getModel()->insert($items);
    }

    /**
     * [delete description].
     *
     * @method delete
     *
     * @author Rahul Agarwal
     *
     * @param array $conditions [description]
     * @return mixed [type] [description]
     */
    public function delete(array $conditions = [])
    {
        $result = $this->getQueryBuilder()->where($conditions)->delete();
        $this->resetQueryBuilder();

        return $result;
    }

    /**
     * @param array $conditions
     * @param array $parameters
     *
     * @return static
     *
     * @author Rahul
     */
    public function updateOrCreate(array $conditions, array $parameters)
    {
        return $this->getModel()->updateOrCreate($conditions, $parameters);
    }

    /**
     * @param array $items
     * @return bool
     */
    public function updateOrCreateMultipleOnUniqueKey(array $items)
    {
        if (!empty($items)) {
            array_walk($items, function (&$val) {
                array_walk($val,function(&$val){
                    $val="'".$val."'";
                });
                return ksort($val);
            });
            $columns = implode(',', array_keys(reset($items)));

            $values=implode(',',array_map(function ($val){
                return '(' . implode(', ', $val) . ')';
            },$items));

            $updateData=implode(', ',array_map(function($val){
                return "$val =  VALUES($val)";
            },array_keys(reset($items))))   ;

            $temp = DB::statement('INSERT INTO ' . $this->getModel()->getTable() . '(' . $columns . ') VALUES '.$values.' ON DUPLICATE KEY UPDATE '.$updateData);
        }
        return true;
    }

    /**
     * @param array $conditions
     * @param array $parameters
     *
     * @return BaseRepository
     *
     * @author Rahul
     */
    public function getOrCreate(array $conditions, array $parameters)
    {
        if (!is_null($instance = $this->getModel()->where($conditions)->first())) {
            return $instance;
        }

        return $this->create($parameters);
    }

    /**
     * @param array $parameters
     * @param array $conditions
     *
     * @return mixed
     *
     * @author Rahul
     */
    public function update(array $parameters, array $conditions = [])
    {
        $model = $this->getQueryBuilder()->where($conditions)->update($parameters);
        $this->resetQueryBuilder();

        return $model;
    }

    /**
     * @param array $parameters
     * @param array $conditions
     * @return mixed
     * @author Rahul
     */
    public function updateMultiple(array $parameters, array $conditions = [])
    {
        $model = $this->getQueryBuilder();
        if (!empty($conditions)) {
            foreach ($conditions as $field => $condition) {
                $model = $model->whereIn($field, $condition);
            }
        }
        $model = $model->update($parameters);
        $this->resetQueryBuilder();

        return $model;
    }

    /**
     * @param $id
     * @param       $relation
     * @param array $values
     * @return mixed
     * @internal param Model $model
     * @author Rahul
     */
    public function syncWithRelation($id, $relation, array $values)
    {
        $model = $this->getModel()->where($this->model->getKeyName(), $id)->first();

        if (empty($model)) {
            return;
        }

        return $model->$relation()->sync($values);
    }

    /**
     * @param $id
     * @param $relation
     * @param array $values
     */
    public function syncWithRelationWithoutDetaching($id, $relation, array $values)
    {
        $model = $this->getModel()->where($this->model->getKeyName(), $id)->first();
        if (empty($model)) {
            return;
        }

        return $model->$relation()->syncWithoutDetaching($values);
    }


    /**
     * @param $id
     * @param $relation
     * @param array $values
     * @author Rahul
     */
    public function updateExistingPivot($id, $relation, array $values){
        $model = $this->getModel()->where($this->model->getKeyName(), $id)->first();
        if (empty($model)) {
            return;
        }

        return $model->$relation()->updateExistingPivot($values);
    }

    /**
     * @param $id
     * @param       $relation
     * @param array $data
     * @return mixed
     * @internal param Model $model
     * @author Rahul
     */
    public function createRelation($id, $relation, array $data)
    {
        $model = $this->getModel()->where($this->model->getKeyName(), $id)->first();
        if (empty($model)) {
            return;
        }

        return $model->$relation()->create($data);
    }

    /**
     * @param $id
     * @param       $relation
     * @param array $data
     * @return mixed
     * @internal param Model $model
     * @author Rahul
     */
    public function attachRelation($id, $relation, array $data)
    {
        $model = $this->getModel()->where($this->model->getKeyName(), $id)->first();
        if (empty($model)) {
            return;
        }

        return $model->$relation()->attach($data);
    }

    /**
     * @param $id
     * @param       $relation
     * @param array $data
     * @return mixed
     * @author Rahul
     */
    public function detachRelation($id, $relation, array $data)
    {
        $model = $this->getModel()->where($this->model->getKeyName(), $id)->first();
        if (empty($model)) {
            return;
        }

        return $model->$relation()->detach($data);
    }

    public function associateRelation($id, $relation, Model $relationModel)
    {
        $model = $this->getModel()->where($this->model->getKeyName(), $id)->first();
        if (empty($model)) {
            return;
        }

        return $model->$relation()->associate($relationModel);
    }

    public function dissociateRelation($id, $relation, Model $relationModel)
    {
        $model = $this->getModel()->where($this->model->getKeyName(), $id)->first();
        if (empty($model)) {
            return;
        }

        return $model->$relation()->dissociate($relationModel);
    }

    /**
     * @param array $conditions
     *
     * @return mixed
     *
     * @author Rahul
     */
    public function getCount(array $conditions = [])
    {
        return $this->getQueryBuilder()->where($conditions)->count();
    }


    /**
     * @return mixed
     */
    public function getQueryBuilder()
    {
            return $this->queryBuilder;
    }

    /**
     * @param $queryBuilder
     *
     * @return mixed
     *
     * @author Rahul
     */
    public function setQueryBuilder($queryBuilder)
    {
        return $this->queryBuilder = $queryBuilder;
    }

    /**
     * @author Rahul
     */
    private function resetQueryBuilder()
    {
        $this->setQueryBuilder($this->getModel());
    }

    /**
     * @param       $field
     * @param array $conditions
     *
     * @return $this
     *
     * @author Rahul
     */
    public function conditionIn($field, array $conditions)
    {
        $this->setQueryBuilder($this->getQueryBuilder()
            ->whereIn($field, $conditions));

        return $this;
    }

    /**
     * @param        $field
     * @param string $order
     *
     * @return $this
     *
     * @author Rahul
     */
    public function sortBy($field, $order = 'asc')
    {
        $this->setQueryBuilder($this->getQueryBuilder()
            ->orderBy($field, $order));

        return $this;
    }

    public function conditionOr($column, $operator = null, $value = null)
    {
        return $this->condition($column, $operator, $value, 'or');
    }

    /**
     * @param        $column
     * @param null   $operator
     * @param null   $value
     * @param string $boolean
     *
     * @return $this
     *
     * @author Rahul
     */
    public function condition($column, $operator = null, $value = null, $boolean = 'and')
    {
        $this->setQueryBuilder($this->getQueryBuilder()
            ->where($column, $operator, $value, $boolean));

        return $this;
    }

    /**
     * @param array $conditions
     *
     * @return mixed
     *
     * @author Rahul
     */
    public function countRows($conditions = [])
    {
        return $this->getModel()->where($conditions)->count();
    }

    /**
     * This function is used for returning the Query
     * Model used in generating Grid in our application.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getQuery()
    {
        return $this->getModel()->newQuery();
    }

    /**
     * @param $field
     *
     * @return array
     *
     * @author Rahul
     */
    public function getEnum($field)
    {
        $table = $this->getModel()->getTable();
        $type = DB::select(DB::raw("SHOW COLUMNS FROM $table WHERE Field = '$field'"))[0]->Type;
        preg_match('/^enum\(\'(.*)\'\)$/', $type, $matches);
        $enum = explode("','", $matches[1]);
        $enum = array_map('ucwords', array_combine($enum, $enum));

        return $enum;
    }

    public function increment($field, $value = 1)
    {
        $result = $this->getQueryBuilder()->increment($field, $value);
        $this->resetQueryBuilder();

        return $result;
    }

    public function decrement($field, $value = 1)
    {
        $result = $this->getQueryBuilder()->decrement($field, $value);
        $this->resetQueryBuilder();

        return $result;
    }

    /**
     * @param $value
     *
     * @return $this
     * @author Rahul
     */
    public function limit($value)
    {
        $this->setQueryBuilder($this->getQueryBuilder()
            ->limit($value));
        return $this;
    }

    /**
     * @return mixed
     * @author Rahul
     */
    public function forceDelete()
    {
        $result = $this->getQueryBuilder()->forceDelete();
        $this->resetQueryBuilder();
        return $result;
    }

    /**
     * @return mixed
     * @author Rahul
     */
    public function restore()
    {
        $result = $this->getQueryBuilder()->restore();
        $this->resetQueryBuilder();
        return $result;
    }

    /**
     * @param $relation
     * @param Closure|null $callback
     * @return $this
     * @author Rahul
     */
    public function withRelation($relation)
    {
        $this->getQueryBuilder()->with($relation);
        return $this;
    }

    /**
     * @param array $columns
     * @return $this
     * @author Rahul
     */
    public function columns(array $columns){
        $this->setQueryBuilder($this->getModel()->addSelect($columns));
        return $this;
    }

    /**
     * @param $relation
     * @param Closure|null $callback
     * @return $this
     * @author Rahul
     */
    public function hasRelation($relation, Closure $callback = null)
    {
        $this->setQueryBuilder($this->getQueryBuilder()
            ->whereHas($relation, $callback));
        return $this;
    }

}
