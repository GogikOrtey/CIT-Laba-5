<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Connection;

class UserController extends AbstractController
{

    /**
     * @Route("/user", methods={"POST"})
     */
    public function createUser(Connection $connection, Request $request): Response
    {
        $data = $request->request->all();

        $sql = 'INSERT INTO Пользователи (ИмяПользователя, АдресЭлектроннойПочты, IDpоли) VALUES (?, ?, ?)';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $data['ИмяПользователя']);
        $stmt->bindValue(2, $data['АдресЭлектроннойПочты']);
        $stmt->bindValue(3, $data['IDpоли']);
        $stmt->executeQuery();

        return new Response('', Response::HTTP_CREATED);
    }

    /**
     * @Route("/user/{id}", methods={"PUT"})
     */
    public function updateUser(Connection $connection, Request $request, $id): Response
    {
        $data = $request->request->all();

        $sql = 'UPDATE Пользователи SET ИмяПользователя = ?, АдресЭлектроннойПочты = ?, IDpоли = ? WHERE IDпользователя = ?';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $data['ИмяПользователя']);
        $stmt->bindValue(2, $data['АдресЭлектроннойПочты']);
        $stmt->bindValue(3, $data['IDpоли']);
        $stmt->bindValue(4, $id);
        $stmt->executeQuery();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

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
}
