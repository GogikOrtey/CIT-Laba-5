<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Connection;
use App\Security\KeycloakJwtBearerHandler; // Подключаем наш обработчик токенов

class StatusController extends AbstractController
{
    private $jwtHandler;

    public function __construct(KeycloakJwtBearerHandler $jwtHandler)
    {
        $this->jwtHandler = $jwtHandler;
    }

    /**
     * @Route("/status", methods={"POST"})
     */
    public function createStatus(Connection $connection, Request $request): Response
    {
        // Проверяем токен
        $error = $this->jwtHandler->handleToken($request);
        if ($error) {
            return $this->json(['error' => $error], 401); // Если есть ошибка, возвращаем ее, и прерываем выполнение метода
        }

        $data = $request->request->all();

        $sql = 'INSERT INTO Статусы (НазваниеСтатуса) VALUES (?)';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $data['НазваниеСтатуса']);
        $stmt->executeQuery();

        return new Response('', Response::HTTP_CREATED);
    }

    /**
     * @Route("/status/{id}", methods={"PUT"})
     */
    public function updateStatus(Connection $connection, Request $request, $id): Response
    {
        // Проверяем токен
        $error = $this->jwtHandler->handleToken($request);
        if ($error) {
            return $this->json(['error' => $error], 401); // Если есть ошибка, возвращаем ее, и прерываем выполнение метода
        }

        $data = $request->request->all();

        $sql = 'UPDATE Статусы SET НазваниеСтатуса = ? WHERE IDстатуса = ?';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $data['НазваниеСтатуса']);
        $stmt->bindValue(2, $id);
        $stmt->executeQuery();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/status/{id}", methods={"GET"})
     */
    public function fetchStatus(Connection $connection, $id): Response
    {
        $sql = 'SELECT * FROM Статусы WHERE IDстатуса = ?';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $id);
        $result = $stmt->executeQuery();

        $status = $result->fetchAssociative();

        return $this->json($status);
    }

    /**
     * @Route("/status", methods={"GET"})
     */
    public function getStatuses(Connection $connection): Response
    {
        $sql = 'SELECT * FROM Статусы';
        $stmt = $connection->prepare($sql);
        $result = $stmt->executeQuery();

        $statuses = $result->fetchAllAssociative();

        return $this->json($statuses);
    }

    /**
     * @Route("/status/{id}", methods={"DELETE"})
     */
    public function deleteStatus(Connection $connection, $id, Request $request): Response
    {
        // Проверяем токен
        $error = $this->jwtHandler->handleToken($request);
        if ($error) {
            return $this->json(['error' => $error], 401); // Если есть ошибка, возвращаем ее, и прерываем выполнение метода
        }
        
        $sql = 'DELETE FROM Статусы WHERE IDстатуса = ?';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->executeQuery();

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
