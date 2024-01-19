<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Connection;
use App\Security\KeycloakJwtBearerHandler; // Подключаем наш обработчик токенов

class AssignmentController extends AbstractController
{
    private $jwtHandler;

    public function __construct(KeycloakJwtBearerHandler $jwtHandler)
    {
        $this->jwtHandler = $jwtHandler;
    }

    /**
     * @Route("/assignment", methods={"POST"})
     */
    public function createAssignment(Connection $connection, Request $request): Response
    {
        // Проверяем токен
        $error = $this->jwtHandler->handleToken($request);
        if ($error) {
            return $this->json(['error' => $error], 401); // Если есть ошибка, возвращаем ее, и прерываем выполнение метода
        }

        $data = $request->request->all();

        $sql = 'INSERT INTO Назначения (IDпользователя, IDзадачи, ДатаНазначения) VALUES (?, ?, ?)';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $data['IDпользователя']);
        $stmt->bindValue(2, $data['IDзадачи']);
        $stmt->bindValue(3, $data['ДатаНазначения']);
        $stmt->executeQuery();

        return new Response('', Response::HTTP_CREATED);
    }

    /**
     * @Route("/assignment/{id}", methods={"PUT"})
     */
    public function updateAssignment(Connection $connection, Request $request, $id): Response
    {
        // Проверяем токен
        $error = $this->jwtHandler->handleToken($request);
        if ($error) {
            return $this->json(['error' => $error], 401); // Если есть ошибка, возвращаем ее, и прерываем выполнение метода
        }

        $data = $request->request->all();

        $sql = 'UPDATE Назначения SET IDпользователя = ?, IDзадачи = ?, ДатаНазначения = ? WHERE IDназначения = ?';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $data['IDпользователя']);
        $stmt->bindValue(2, $data['IDзадачи']);
        $stmt->bindValue(3, $data['ДатаНазначения']);
        $stmt->bindValue(4, $id);
        $stmt->executeQuery();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/assignment/{id}", methods={"GET"})
     */
    public function fetchAssignment(Connection $connection, $id): Response
    {
        $sql = 'SELECT * FROM Назначения WHERE IDназначения = ?';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $id);
        $result = $stmt->executeQuery();

        $assignment = $result->fetchAssociative();

        return $this->json($assignment);
    }

    /**
     * @Route("/assignment", methods={"GET"})
     */
    public function getAssignments(Connection $connection): Response
    {
        $sql = 'SELECT * FROM Назначения';
        $stmt = $connection->prepare($sql);
        $result = $stmt->executeQuery();

        $assignments = $result->fetchAllAssociative();

        return $this->json($assignments);
    }

    /**
     * @Route("/assignment/{id}", methods={"DELETE"})
     */
    public function deleteAssignment(Connection $connection, $id, Request $request): Response
    {
        // Проверяем токен
        $error = $this->jwtHandler->handleToken($request);
        if ($error) {
            return $this->json(['error' => $error], 401); // Если есть ошибка, возвращаем ее, и прерываем выполнение метода
        }
        
        $sql = 'DELETE FROM Назначения WHERE IDназначения = ?';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->executeQuery();

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
