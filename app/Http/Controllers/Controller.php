<?php

namespace App\Http\Controllers;

abstract class Controller {
    /**
     * Summary of successMessage
     * @param mixed $message
     * @param mixed $data
     * @param mixed $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function successMessage($message, $data, $code) {
        return response()->json([
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Summary of failMessage
     * @param mixed $message
     * @param mixed $error
     * @param mixed $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function failMessage($message, $error, $code) {
        return response()->json([
            'message' => $message,
            'error' => $error
        ], $code);
    }
}
