<?php

namespace App\Response;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Fractal\Fractal;

trait ApiResponser
{
    protected function saccess( Model $instance, $code = 201 , $message = 'The operation was successful', $state= true) {
        return  $this->successResponse(['data' => $instance,'success'=>true, 'message' => $message, 'status_code' => $code, 'state' => $state], $code);
    }
    
    protected function authResponse($code = 200, $message = 'El usuario fue registrado',$state = true)
    {
        return response()->json(['success'=>$state, 'message'=>$message, 'status_code' => $code, 'state' => $state], $code);
    }
    
    protected function responseWithArray( $instance, $code = 201 , $message = 'The operation was successful', $state= true) {
        return  $this->successResponse(['data' => $instance,'success'=>true, 'message' => $message, 'status_code' => $code, 'state' => $state], $code);
    }
	private function successResponse($data, $code)
	{
		return response()->json($data, $code);
	}
        
    protected function sendResetPasswordResponse($code = 200, $message = 'El email para reestablecer la contraseña fue enviado',$state = true)
    {
        return response()->json(['success'=>$state, 'message'=>$message, 'status_code' => $code, 'state' => $state], $code);
    }
    
    protected function professionalScheduleResponse($appointment,$code = 200, $message = 'El horario del professional fue actualizado',$state = true)
    {
        return response()->json(['success'=>$state, 'message'=>$message,'appointmentObject'=>$appointment, 'status_code' => $code, 'state' => $state], $code);
    }
    
    protected function certificationResponse($code = 200, $message = 'Peticion de certificacion enviada correctamente',$state = true)
    {
        return response()->json(['success'=>$state, 'message'=>$message, 'status_code' => $code, 'state' => $state], $code);
    }
    
    protected function voteResponse($code = 200, $message = 'Votación guardada exitosamente',$state = true)
    {
        return response()->json(['success'=>$state, 'message'=>$message, 'status_code' => $code, 'state' => $state], $code);
    }
    
    protected function professionalSavedScheduleResponse($code = 200, $message = 'El horario del professional fue actualizado',$state = true)
    {
        return response()->json(['success'=>$state, 'message'=>$message, 'status_code' => $code, 'state' => $state], $code);
    }
    
    protected function professionalScheduleErrorResponse($itemArray,$code = 200, $message = 'El horario del professional fue actualizado',$state = true)
    {
        return response()->json(['success'=>$state, 'message'=>$message,'itemsWithProblem'=>$itemArray, 'status_code' => $code, 'state' => $state], $code);
    }
    
    protected function appointmentResponse($code = 200, $message = 'El horario del professional fue actualizado',$state = true)
    {
        return response()->json(['success'=>$state, 'message'=>$message, 'status_code' => $code, 'state' => $state], $code);
    }

    public function responsePaginate($data)
    {
        return response()->json(['data' => $data], 200);
    }

	protected function errorResponse($message, $code)
	{
		return response()->json(['error' => $message,'success'=>false, 'code' => $code], $code);
	}

	protected function showAll(Collection $collection, $code = 200)
	{
		if ($collection->isEmpty()) {
			return $this->successResponse(['data' => $collection], $code);
		}

		$transformer = $collection->first()->transformer;

		$collection = $this->filterData($collection, $transformer);
		$collection = $this->sortData($collection, $transformer);
		$collection = $this->paginate($collection);
		$collection = $this->transformData($collection, $transformer);
		$collection = $this->cacheResponse($collection);


		return $this->successResponse($collection, $code);
	}

	protected function showOne(Model $instance, $code = 200)
	{
		$transformer = $instance->transformer;
		$instance = $this->transformData($instance, $transformer);

		return $this->successResponse($instance, $code);
	}

	protected function showMessage($message, $code = 200)
	{
		return $this->successResponse(['data' => $message], $code);
	}

	protected function filterData(Collection $collection, $transformer)
	{
		foreach (request()->query() as $query => $value) {
			$attribute = $transformer::originalAttribute($query);

			if (isset($attribute, $value)) {
				$collection = $collection->where($attribute, $value);
			}
		}

		return $collection;
	}

	protected function sortData(Collection $collection, $transformer)
	{
		if (request()->has('sort_by')) {
			$attribute = $transformer::originalAttribute(request()->sort_by);

			$collection = $collection->sortBy->{$attribute};
		}
		return $collection;
	}

	protected function paginate(Collection $collection)
	{
		$rules = [
			'per_page' => 'integer|min:2|max:50'
		];

		Validator::validate(request()->all(), $rules);

		$page = LengthAwarePaginator::resolveCurrentPage();

		$perPage = 15;
		if (request()->has('per_page')) {
			$perPage = (int) request()->per_page;
		}

		$results = $collection->slice(($page - 1) * $perPage, $perPage)->values();

		$paginated = new LengthAwarePaginator($results, $collection->count(), $perPage, $page, [
			'path' => LengthAwarePaginator::resolveCurrentPath(),
		]);

		$paginated->appends(request()->all());

		return $paginated;
	}

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

		return Cache::remember($fullUrl, 30/60, function() use($data) {
			return $data;
		});
	}
}
