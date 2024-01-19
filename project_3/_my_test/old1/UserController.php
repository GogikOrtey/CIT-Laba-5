<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Connection;

class UserController extends AbstractController
{
    // 
	// /**
    //  * @Route("/user", methods={"POST"})
    //  */
    // public function createUser(Connection $connection, Request $request): Response
    // {
    //     $data = json_decode($request->getContent(), true);

    //     $sql = 'INSERT INTO Пользователи (IDпользователя, ИмяПользователя) VALUES (?, ?)';
    //     $stmt = $connection->prepare($sql);
    //     $stmt->bindValue(1, $data['IDпользователя']);
    //     $stmt->bindValue(2, $data['ИмяПользователя']);
    //     $stmt->executeQuery();

    //     return new Response('', Response::HTTP_CREATED);
    // }

    // public function createUser(Connection $connection, Request $request): Response
    // {
    //     $data = $request->request->all();

    //     $sql = 'INSERT INTO Пользователи (IDпользователя, ИмяПользователя) VALUES (?, ?)';
    //     $stmt = $connection->prepare($sql);
    //     $stmt->bindValue(1, $data['IDпользователя']);
    //     $stmt->bindValue(2, $data['ИмяПользователя']);
    //     $stmt->executeQuery();

    //     return new Response('', Response::HTTP_CREATED);
    // }

    /**
     * @Route("/user", methods={"POST"})
     */
    public function createUser(Connection $connection, Request $request): Response
    {
        $data = $request->request->all();

        $sql = 'INSERT INTO Пользователи (ИмяПользователя) VALUES (?)';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $data['ИмяПользователя']);
        $stmt->executeQuery();

        return new Response('', Response::HTTP_CREATED);
    }

    /**
     * @Route("/user/{id}", methods={"PUT"})
     */
    public function updateUser(Connection $connection, Request $request, $id): Response
    {
        //$data = json_decode($request->getContent(), true);
        $data = $request->request->all();

        $sql = 'UPDATE Пользователи SET (ИмяПользователя) = (?) WHERE (IDпользователя) = (?)';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $data['ИмяПользователя']);
        $stmt->bindValue(2, $id);
        $stmt->executeQuery();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    //  /**
    //  * @Route("/user/{id}", methods={"PUT"})
    //  */
    // public function updateUser(Connection $connection, Request $request, $id): Response
    // {
    //     $data = json_decode($request->getContent(), true);

    //     $sql = 'UPDATE Пользователи SET ИмяПользователя = ? WHERE IDпользователя = ?';
    //     $stmt = $connection->prepare($sql);
    //     $stmt->bindValue(1, $data['ИмяПользователя']);
    //     $stmt->bindValue(2, $id);
    //     $stmt->executeQuery();

    //     return new Response('', Response::HTTP_NO_CONTENT);
    // }

    /**
     * @Route("/user/{id}", methods={"GET"})
     */
    public function fetchUser(Connection $connection, $id): Response
    {
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

    /**
     * @Route("/user/{id}", methods={"DELETE"})
     */
    public function deleteUser(Connection $connection, $id): Response
    {
        $sql = 'DELETE FROM Пользователи WHERE IDпользователя = ?';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->executeQuery();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    // /**
    //  * @Route("/user/{id}/tasks", methods={"GET"})
    //  */
    // public function fetchUserTasks(Connection $connection, $id): Response
    // {
    //     $sql = 'SELECT Задачи.IDзадачи, Задачи.ОписаниеЗадачи FROM Задачи JOIN Назначения ON Задачи.IDзадачи = Назначения.IDзадачи WHERE Назначения.IDпользователя = ?';
    //     $stmt = $connection->prepare($sql);
    //     $stmt->bindValue(1, $id);
    //     $result = $stmt->executeQuery();

    //     $tasks = $result->fetchAllAssociative();

    //     return $this->json($tasks);
    // }

    /**
     * @Route("/user/{id}/tasks", methods={"GET"})
     */
    public function fetchUserTasks(Connection $connection, $id): Response
    {
        $sql = 'SELECT * FROM Пользователи WHERE IDпользователя = ?';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $id);
        $result = $stmt->executeQuery();
    
        $user = $result->fetchAssociative();
    
        if (!$user) {
            return new Response('Пользователь не найден', Response::HTTP_NOT_FOUND);
        }
    
        $sql = 'SELECT Задачи.IDзадачи, Задачи.ОписаниеЗадачи FROM Задачи JOIN Назначения ON Задачи.IDзадачи = Назначения.IDзадачи WHERE Назначения.IDпользователя = ?';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $id);
        $result = $stmt->executeQuery();
    
        $tasks = $result->fetchAllAssociative();
    
        return $this->json($tasks);
    }
}
