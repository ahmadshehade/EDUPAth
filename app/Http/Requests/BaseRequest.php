<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseRequest extends FormRequest
{

   /**
    * Summary of failedValidation
    * @param \Illuminate\Contracts\Validation\Validator $validator
    * @return HttpResponseException
    */
   public function  failedValidation(\Illuminate\Contracts\Validation\Validator $validator){
     return new HttpResponseException(response()->json([
         'error'=>$validator->errors()
     ],422));
   }

}
