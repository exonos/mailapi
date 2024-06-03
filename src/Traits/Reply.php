<?php

namespace Exonos\Mailapi\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait Reply {

    /** Return success response
     * @param string $message
     * @return JsonResponse
     */

    public static function success($message, $code=200)
    {
        return response()->json([
            'success'   => true,
            'message'   => self::getTranslated($message),
            'status'   => $code,
        ], $code);
    }

    /**
     * Return success response with additional data
     * @param string $message
     * @param array $data
     * @return JsonResponse
     */
    public static function successWithData($message, $data = [], $code = 200)
    {
        $response = self::success($message, $code);
        $responseData = $response->getData(true);

        // Verificar si $data es un array o un objeto
        if (is_array($data)) {
            $responseData['data'] = $data;
        } else {
            $responseData['data'] = get_object_vars($data);
        }

        $response->setData($responseData);
        return $response;
    }

    /**
     * Return success response with additional data
     * @param string $message
     * @param array $data
     * @return JsonResponse
     */
    public static function errorWithData($message, $data = [], $error = Response::HTTP_BAD_REQUEST)
    {
        $response = self::error($message, $error);
        $responseData = $response->getData(true);

        // Verificar si $data es un array o un objeto
        if (is_array($data)) {
            $responseData['data'] = $data;
        } else {
            $responseData['data'] = get_object_vars($data);
        }

        $response->setData($responseData);
        $response->setData($responseData);
        return $response;
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    public static function error($message, $code = 400)
    {
        return response()->json([
            'success'   => false,
            'message'   => $message,
            'status'   => $code
        ], $code);
    }

    /** Return validation errors
     * @param \Illuminate\Validation\Validator|Validator $validator
     * @return array
     */
    public static function formErrors($validator)
    {
        return [
            'success'   => false,
            'message'   => 'Error de validacion',
            'status'   => 400,
            'data'      => $validator->getMessageBag()->toArray(),
        ];
    }


    /** Return validation errors
     * @param \Illuminate\Validation\Validator|Validator $validator
     * @return array
     */
    public static function successWithCustomValidationCode(array $data, string $message)
    {
        return [
            'success'   => true,
            'message'   => $message,
            'status'   => 200,
            'data'      => $data
        ];
    }

    public static function errorWithCustomValidationCode(string $message, int $status)
    {
        return [
            'success'   => false,
            'message'   => $message,
            'status'   => $status
        ];
    }

    private static function getTranslated($message)
    {
        $trans = trans($message);

        if ($trans == $message) {
            return $message;
        }

        return $trans;

    }

    public static function dataOnly($data)
    {
        return $data;
    }

}
