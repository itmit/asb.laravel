<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse as JsonResponseAlias;
use App\Models\User;
use App\Models\Client;

/**
 * Представляет базовый класс для api контроллеров.
 *
 * @package App\Http\Controllers\Api
 */
abstract class ApiBaseController extends Controller
{
    /**
     * Возвращает id представителства к которому отностися диспетчер или клиент.
     * Либо текущий id пользователя если он является представителем или админом.
     *
     * @return integer
     */
    protected function getRepresentativeId(): int
    {
        $user = auth('api')->user();
        if ($user instanceof Client) 
        {
            return $user->representative;
        }

        return 0;
    }

    /**
     * Отправляет ответ на успешный запрос к api.
     *
     * @param array $result
     * @param string $message
     * @return JsonResponseAlias
     */
    public function sendResponse(array $result, string $message): JsonResponseAlias
    {
        return response()->json([
            'success' => true,
            'data' => $result,
            'message' => $message,
        ], 200);
    }

    /**
     * Отправляет ответ на запрос к api в случае ошибки.
     *
     * @param string $error
     * @param array $errorMessages
     * @param int $code
     * @return JsonResponseAlias
     */
    public function sendError(string $error, $errorMessages = [], int $code = 404): JsonResponseAlias
    {
        $response = [
            'success' => false,
            'data' => $errorMessages,
            'message' => $error,
        ];

        return response()->json($response, $code);
    }
}
