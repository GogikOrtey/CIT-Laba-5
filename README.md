## 5 лаба Сетевые - шаги выполнения

Перейти в директорию проекта на Sinfony

```
cd /home/kali/Symfony_and_Doctrine_2/project_3
```

Запустить основной сервер на Sinfony

```
php -S 127.0.0.1:8000 -t public/
```

Перейти на страницу:

```
http://localhost:3000/
```

---

Перейти в директорию проекта на Swagger

```
cd /home/kali/Symfony_and_Doctrine_2/Swagger_02
```

Запустить сервер с Swagger UI

```
node server.js
```

Он будет доступен по адресу:

```
http://localhost:8000
```

UI там нет, можно только запросики на него слать

---

Проверить, что контейнер с Keycloak запущен:

```
sudo su
docker ps
```

---

---

Настройка Keycloak:

Переходим на страницу 

```
http://localhost:8080/
```

Логин и пароль от Keycloak:

```
admin
Password23
```

---

Для настройки обработки JWT-токена, внутри нашего основного сервера, нам нужно будет установить пакет, создать новый .php файл с классом, который будет реализовывать проверку, и задать в нём необходимые поля.

Сейчас сосредоточимся на 2х вещах:

1. Найдём в Keycloak секретный ключ

* Переходим в раздел Clients, выбираем нашего клиента. У меня это - Main_Client
* В первом (сверху) разделе Settings, нужно изменить Access Type на confederintial
  * Если поле Valid Redirect URLs ругается на вас, вставьте туда звёздочку (*)
* Далее, на вкладке Credentals (крендельки), будет Secret - секретный ключ. Копируем его, он обычно не изменяется сам по себе

```
Ojh5fdaiXfTvK78yE6TuXDRVOAKEVNKa
```

2. Найдём, где в Keycloak указывается алгоритм подписи
   * Там-же, в разделе Clients, в вашем клиенте, во вкладке Settings, долистайте до "Fine Grain OpenID Connect Configuration", раскройте этот список
   * Нужно будет выбрать алгоритм подписи, в поле "Access Token Signature Algorithm"
   * Лично я выбрал такой:

```
HS256
```

---

Хорошо, мы готовы продолжать работу дальше. Теперь нужно получить access-токен, используя http-запрос, из Insomnia:

Проверяем, что сервер работает:

```
GET http://localhost:8080/realms/main/.well-known/uma2-configuration
```

Получаем access-токен:

**POST**

**URL:** http://localhost:8080/realms/main/protocol/openid-connect/token

**From:**

* Я буду записывать синтаксис аргументов так: **name : value**
* client_id : main_client
* client_secret : Ojh5fdaiXfTvK78yE6TuXDRVOAKEVNKa
* username : test_username
* password : Pass24
* grant_type : password

---

Получили такой access-токен:

```
eyJhbGciOiJSUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICJTTmhhVWFURXZMMmY3QVZ6V2F3YVlrbVAtc3dDYVJSQkNxVWZZVkp2TTNNIn0.eyJleHAiOjE3MDU1OTQyNzUsImlhdCI6MTcwNTU5Mzk3NSwianRpIjoiNjlmYmIxOTQtMzllMy00NTBhLTgxZTAtZGU5ZTAwNDU3OTEyIiwiaXNzIjoiaHR0cDovL2xvY2FsaG9zdDo4MDgwL3JlYWxtcy9uZXdyZWFsbSIsImF1ZCI6ImFjY291bnQiLCJzdWIiOiIxZWIyNGFhMS1kYzM1LTQ5YjMtYjJmMy04NzY0N2Q1Yjk4OGIiLCJ0eXAiOiJCZWFyZXIiLCJhenAiOiJtYWluX2NsaWVudCIsInNlc3Npb25fc3RhdGUiOiIxOGI5Y2Y4Yy0wYTc0LTQwYTktYTFjZC1mMTM1ZGJlMzNjNWEiLCJhY3IiOiIxIiwicmVhbG1fYWNjZXNzIjp7InJvbGVzIjpbIm9mZmxpbmVfYWNjZXNzIiwiZGVmYXVsdC1yb2xlcy1uZXdyZWFsbSIsInVtYV9hdXRob3JpemF0aW9uIl19LCJyZXNvdXJjZV9hY2Nlc3MiOnsibWFpbl9jbGllbnQiOnsicm9sZXMiOlsibWFpbl9yb2xlIiwibWFpbl9yb2xlMiJdfSwiYWNjb3VudCI6eyJyb2xlcyI6WyJtYW5hZ2UtYWNjb3VudCIsIm1hbmFnZS1hY2NvdW50LWxpbmtzIiwidmlldy1wcm9maWxlIl19fSwic2NvcGUiOiJwcm9maWxlIGVtYWlsIiwic2lkIjoiMThiOWNmOGMtMGE3NC00MGE5LWExY2QtZjEzNWRiZTMzYzVhIiwiZW1haWxfdmVyaWZpZWQiOmZhbHNlLCJwcmVmZXJyZWRfdXNlcm5hbWUiOiJ0ZXN0X3VzZXJuYW1lIn0.RToDXs2-CNZZgw1cwcY_T72-Monm2QlskZeqzYbrC3mXfA31BAIT4CdCI-fgGiJm15RoT5R4jmasNoE_czcahQgpH6BDhPAeZhVzP-Ek0iNNOIcAaNQSU5KWnRz9pAKM8QA9b9-FChzRv9MrOBhK-7n514zc45LlyNc2VBPgZMbc9KzkUv0UypcaqEdFa-Oihy2VsWPoXGtrrtnKPTV-5ws2rIRScvlJKFrG8mp5CK61FARbpufMUtr_XWf9cJrP6-UgadUhxF3VNxda1qvAvi3FDXIu5JbXT-_S2Wlr6uWToEwx0NQk2kG5k92hoCBjEPMqVletsjrmdcMLQn6Ilg
```

Помним, что время жизни у него - 5 минут, если мы не указали другое значение. Я вроде бы менял у себя на 1 час, но не точно.

---

Проверяем, что этот access-токен рабочий:

**Получение пользователя, access-токена (GET)**

**URL:** http://localhost:8080/realms/newrealm/protocol/openid-connect/userinfo

**Headers:**

* Authorization : Bearer [access_token]

Так вы получите список всех ваших созданных пользователей

---

---

Отлично, всё работает, можно переходить дальше

Запускаем сервер с Simfony и сервер со Swagger. Убеждаемся, что запросы успешно идут

---

Простой пример класса **KeycloakJwtBearerHandler**

```php
<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;

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

        // Если токен успешно извлечен, не возвращаем ошибку
        return null;
    }
}

```

Этот класс следует поместить в папку `src/Security` вашего проекта Symfony. Имя файла должно быть `KeycloakJwtBearerHandler.php`.

Простой пример изменённого кода GET User, на 2 метода:

```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Connection;
use App\Security\KeycloakJwtBearerHandler; // Подключаем наш обработчик токенов

class UserController extends AbstractController
{
    private $jwtHandler;

    public function __construct(KeycloakJwtBearerHandler $jwtHandler)
    {
        $this->jwtHandler = $jwtHandler;
    }

    /**
     * @Route("/user/{id}", methods={"GET"})
     */
    public function fetchUser(Connection $connection, $id, Request $request): Response
    {
        // Проверяем токен
        $error = $this->jwtHandler->handleToken($request);
        if ($error) {
            return $this->json(['error' => $error], 401); // Если есть ошибка, возвращаем ее
        }

        $sql = 'SELECT * FROM Пользователи WHERE IDпользователя = ?';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $id);
        $result = $stmt->executeQuery();

        $user = $result->fetchAssociative();

        return $this->json($user);
    }

    /**
     * @Route("/user", methods={"GET"})
     */
    public function getUsers(Connection $connection): Response
    {
        $sql = 'SELECT * FROM Пользователи';
        $stmt = $connection->prepare($sql);
        $result = $stmt->executeQuery();

        $users = $result->fetchAllAssociative();

        return $this->json($users);
    }
}

```

---

На этом шаге я добавил роль 

```
ALL_Access
```

В Keycloak, и назначил её моему единственному пользователю.

---

**Получение пользователя, access-токена (GET)**

**URL:** http://localhost:8080/realms/newrealm/protocol/openid-connect/userinfo

**Headers:**

* Authorization : Bearer [access_token]

Вот таким запросом можно получить роли пользователя:

---

Помним, что вся полезная нагрузка, уже лежит внутри JWT-токена, в т.ч. и наши назначенные пользователю роли. Нам осталось только их расшифровать, и проверить - есть ли среди них нужная нам роль.

---

Устанавливаем нужную библиотеку:

```
composer require firebase/php-jwt
```

Вот так дополняем код класса KeycloakJwtBearerHandler

```php
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
        try {
            $decoded = JWT::decode($token, $secretKey, ['HS256']);
            return($decoded); // Выводим всю структуру расшифрованного JWT-токена
        } catch (\Exception $e) {
            return 'Ошибка: ' . $e->getMessage();
        }

        // Если токен успешно извлечен и декодирован, не возвращаем ошибку
        return null;
    }
}
```

```php
<?php

$jwt_payload = json_decode($jwt_payload, true); // Декодируем JWT-токен в массив

$realm_access_roles = $jwt_payload['realm_access']['roles']; // Извлекаем все роли из realm_access

if (!in_array('ALL_Access', $realm_access_roles)) { // Проверяем, есть ли роль ALL_Access среди извлеченных ролей
    return 'Ошибка: Роль ALL_Access отсутствует в realm_access';
}

// Если роль ALL_Access присутствует, продолжаем выполнение кода...

```

Новый токен:

```
eyJhbGciOiJSUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICJTTmhhVWFURXZMMmY3QVZ6V2F3YVlrbVAtc3dDYVJSQkNxVWZZVkp2TTNNIn0.eyJleHAiOjE3MDU2MDE3OTMsImlhdCI6MTcwNTU5ODE5MywianRpIjoiNDAxOTIyYjktZjM2Ni00NzQxLWJhNTEtZTE0ZWY1MTYxZTJkIiwiaXNzIjoiaHR0cDovL2xvY2FsaG9zdDo4MDgwL3JlYWxtcy9uZXdyZWFsbSIsImF1ZCI6ImFjY291bnQiLCJzdWIiOiIxZWIyNGFhMS1kYzM1LTQ5YjMtYjJmMy04NzY0N2Q1Yjk4OGIiLCJ0eXAiOiJCZWFyZXIiLCJhenAiOiJtYWluX2NsaWVudCIsInNlc3Npb25fc3RhdGUiOiJlZjM5ZTQ5YS0yMTYyLTQ1Y2EtOTVmZS0zZGE4ZjM3OWMwOGMiLCJhY3IiOiIxIiwicmVhbG1fYWNjZXNzIjp7InJvbGVzIjpbIm9mZmxpbmVfYWNjZXNzIiwiZGVmYXVsdC1yb2xlcy1uZXdyZWFsbSIsInVtYV9hdXRob3JpemF0aW9uIiwiQUxMX0FjY2VzcyJdfSwicmVzb3VyY2VfYWNjZXNzIjp7Im1haW5fY2xpZW50Ijp7InJvbGVzIjpbIm1haW5fcm9sZSIsIm1haW5fcm9sZTIiXX0sImFjY291bnQiOnsicm9sZXMiOlsibWFuYWdlLWFjY291bnQiLCJtYW5hZ2UtYWNjb3VudC1saW5rcyIsInZpZXctcHJvZmlsZSJdfX0sInNjb3BlIjoicHJvZmlsZSBlbWFpbCIsInNpZCI6ImVmMzllNDlhLTIxNjItNDVjYS05NWZlLTNkYThmMzc5YzA4YyIsImVtYWlsX3ZlcmlmaWVkIjpmYWxzZSwicHJlZmVycmVkX3VzZXJuYW1lIjoidGVzdF91c2VybmFtZSJ9.mccWuSIMDQqVC9FESSdUGJvT_bFNRU6Zyk5eBJtnYeLfuBVBfKqXG-mWTWYAtAQlwjj6-1BHHPLt4g1kTzdEi25bLFgU0jzM7V_t8ByEkOmjeKx-CV05tVs41qNLaniO83MdT74a9BawV1cbteXFyk3SRAYwLjakqVMGxsTFM15vaCp3kYqrC5CLvMoCzI8MruZq2sYaPgaIhQHtomWxbdVEX2CvNe0Ank61IIP8H_kKfmdzNNPY8qDKxc5MSDv-nX87txga36FRyOf2GYfGIeQKbYqT3bnm4JJd5ort3KO99kTYI0zGOVhrlTYCw1yyrzfXIaLs5oQgSYMykJoIig
```

Вот этот сайт может быть полезен для разбора структуры вашего токена:

```
https://jwt.io/
```

В структуру .yaml файла сверху нужно добавить:

```yaml
openapi: "3.0.0"
info:
  version: 1.0.0
  title: Управление задачами игровой студии
servers:
  - url: http://localhost:8000
components:
  securitySchemes:
    BearerAuth:
      type: http
      scheme: bearer
      bearerFormat: JWT
```

И также, для каждого метода с авторизацией, нужно добавить блок

```yaml
      security:
        - BearerAuth: []
```

Вот в этом месте:

```yaml
  /user/{id}:
    get:
      tags:
      - Пользователи
      summary: Get a user by ID
      operationId: fetchUser
      parameters:
      - name: id
        in: path
        required: true
        schema:
          type: integer
      security:			// <-- Вот тут
        - BearerAuth: []
      responses:
        '200':
          description: A user object
          content:
            application/x-www-form-urlencoded:
              schema:
                type: object
                properties:
                  ИмяПользователя:
                    type: string
                  АдресЭлектроннойПочты:
                    type: string
                  IDpоли:
                    type: integer
```

И также, для каждого метода запроса, нужно будет добавить:

В заголовке

```php
use App\Security\KeycloakJwtBearerHandler; // Подключаем наш обработчик токенов
```

В начале класса:

```php
    private $jwtHandler;

    public function __construct(KeycloakJwtBearerHandler $jwtHandler)
    {
        $this->jwtHandler = $jwtHandler;
    }
```

В самом начале метода обработки запроса:

```php
        // Проверяем токен
        $error = $this->jwtHandler->handleToken($request);
        if ($error) {
            return $this->json(['error' => $error], 401); // Если есть ошибка, возвращаем ее, и прерываем выполнение метода
        }
```

B затем, в заголовке метода, нужно добавить 

```php
, Request $request
```

Т.е. было вот так:

```php
public function deleteUser(Connection $connection, $id): Response
```

А должно стать вот так:

```php
public function deleteUser(Connection $connection, $id, Request $request): Response
```

Но это там, где нет этой переменной - только в методах Delete



 











