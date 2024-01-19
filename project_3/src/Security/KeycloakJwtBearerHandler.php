<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Firebase\JWT\JWT;

class KeycloakJwtBearerHandler
{
    public function handleToken(Request $request)
    {
        // Проверка наличия заголовка Authorization
        $authHeader = $request->headers->get('Authorization');
        if (!$authHeader) {
            return 'Ошибка: Заголовок Authorization отсутствует в запросе';
        }

        // Извлечение значения токена из заголовка
        $token = str_replace('Bearer ', '', $authHeader);
        if (!$token || strlen($token) < 10) {
            return 'Ошибка: Недействительный токен в заголовке Authorization';
        }

        // Декодирование токена
        $secretKey = 'Ojh5fdaiXfTvK78yE6TuXDRVOAKEVNKa';
        $allowed_algs = ['HS256'];
        $decoded = [];
        try {
            list($headb64, $bodyb64, $cryptob64) = explode('.', $token);
            $payload = JWT::urlsafeB64Decode($bodyb64);
            $decoded = json_decode($payload, true);
            //return($decoded); // Выводим всю структуру расшифрованного JWT-токена

            $realm_access_roles = $decoded['realm_access']['roles']; // Извлекаем все роли из realm_access

            if (!in_array('ALL_Access', $realm_access_roles)) { // Проверяем, есть ли роль ALL_Access среди извлеченных ролей
                return 'Ошибка: Роль ALL_Access отсутствует в realm_access';
            }

        } catch (\Exception $e) {
            return 'При разборе токена произошла ошибка: ' . $e->getMessage() . ', скорее всего, вы ввели неверный токен';
        }
        //return 'Role Access Sucsessfull!';

        // Если токен успешно извлечен и декодирован, не возвращаем ошибку
        return null;
    }
}