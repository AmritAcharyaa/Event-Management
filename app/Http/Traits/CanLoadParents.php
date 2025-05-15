<?php
namespace App\Http\Traits;

use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

trait CanLoadParents{

    public function loadParents(
    Model | EloquentBuilder | QueryBuilder $for, ? array $relations = []
    ){
            $relations = $this->relations ?? $relations ?? [];
            
        foreach($relations as $relation){
            
            $for->when(
                $this->checkRelationship($relation),

                fn($query)=> ($for instanceof Model)? $for->load($relation) : $query->with($relation),
            );
        }
        return $for;
    }

    public function checkRelationship($relation) {

        $include = request('include');
        if(!$include){
            return false;
        }

        $relations = array_map('trim',explode(',',$include));
      return in_array($relation, $relations);

    }


}