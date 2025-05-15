<?php
namespace App\Http\Traits;

use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait CanLoadRelationship{

public function loadRelationship(
Model| EloquentBuilder |QueryBuilder | HasMany $for,
? array $relations =[]
):Model| EloquentBuilder |QueryBuilder | HasMany{
    $relations = $relations ?? $this->$relations ?? [];

    foreach($relations as $relation){
        $for->when(
         $this->shouldIncludeRelationship($relation),
         fn($query)=> $for instanceof Model ? $for->load($relation) :  $query->with($relation) ,  //true ko case ma in when funtion
         // fn($query)=>$query->with('user') //false ko case ma in when function
        );
     }
     return $for;


}
protected function shouldIncludeRelationship(string $relation){
    $include = request('include');
    
    if(!$include){
        return false;
    }
   $relations = array_map('trim',explode(',',$include));
   
   return in_array($relation, $relations);

}

}