<?php

namespace App\Traits;
use Illuminate\Support\Collection;
use  Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponser
{
    //ssuccess response 
    private function successResponse($data, $code)
    {
        return response()->json($data, $code);
    }

    //Gives error message
    protected function errorResponse($message, $code)
    {
      return response()->json(['error'=> $message, 'code'=> $code], $code);
    }

    //show all the data 
    protected function showAll(Collection $collection, $code = 200)        
    {
     if($collection->isEmpty())
     {
          return $this->successResponse(['data'=>$collection], $code);
     }   

       $transformer = $collection->first()->transformer;

       $collection = $this->filterData($collection, $transformer);
       $collection = $this->sortData($collection, $transformer);
       $collection = $this->paginate($collection);
       $collection = $this->transformData($collection, $transformer);
       $collection = $this->cacheResponse($collection);

       return $this->successResponse($collection, $code);
    }
     
    //show one data
    protected function showOne(Model $instance, $code = 200)        
    {
        $transformer = $instance->transformer;
        $instance = $this->transformData($instance, $transformer);
        return $this->successResponse($instance, $code);
    }
    
    //show message
    protected function showMessage($message , $code = 200)
    {
        return $this->successResponse(['data'=>$message],$code);
    }

    //filter the data
    protected function filterData(Collection $collection, $transformer)
    {
        foreach(request()->query() as $query=>$value)
        {
            $attribute = $transformer::originalAttribute($query);
            
            if (isset($attribute, $value)) 
            {
                $collection = $collection->where($attribute,$value);
            }
        }

        return $collection;
    }

    //sort the data    
    protected function sortData(Collection $collection, $transformer)
    {
        if(request()->has('sort_by'))
        {
            $attribute = $transformer::originalAttribute(request()->sort_by);

            $collection = $collection->sortBy->{$attribute};
        }
        return $collection;
    }

    /**pagninate the data*/
    protected function paginate(Collection $collection)
    {
        $rules =[
            'per_page'=>'integer|min:2|max:50',
            ];

            Validator::validate(request()->all(), $rules);

            $page = LengthAwarePaginator::resolveCurrentPage();

            $perPage = 15;
            if(request()->has('per_page'))
            {
                $perPage = (int) request()->per_page;
            }

            $results = $collection->slice(($page - 1) * $perPage,$perPage)->values();

            $paginated = new LengthAwarePaginator($results, $collection->count(),$perPage, $page, [
            'path'=>LengthAwarePaginator::resolveCurrentPath(),
            ]);

            $paginated->appends(request()->all());
            return $paginated;
    }
    
    //transform the data
    protected function transformData($data, $transformer)
    {
        $transformation = fractal($data, new $transformer);

        return $transformation->toArray();
    }

    protected function cacheResponse($data)
    {
        $url = request()->url();
        $queryParams = request()->query();

        ksort($queryParams);

        $queryString = http_build_query($queryParams);

        $fullUrl = "{$url}?{$queryString}";
        return Cache::remember($fullUrl, 30/60, function() use ($data){
            return $data;
        });

    }
}