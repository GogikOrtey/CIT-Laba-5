<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Connection;

class TaskController extends AbstractController
{

    /**
     * @Route("/task", methods={"POST"})
     */
    public function createTask(Connection $connection, Request $request): Response
    {
        $data = $request->request->all();

        $sql = 'INSERT INTO Задачи (ОписаниеЗадачи) VALUES (?)';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $data['ОписаниеЗадачи']);
        $stmt->executeQuery();

        return new Response('', Response::HTTP_CREATED);
    }

    /**
     * @Route("/task/{id}", methods={"PUT"})
     */
    public function updateTask(Connection $connection, Request $request, $id): Response
    {
        $data = $request->request->all();

        $sql = 'UPDATE Задачи SET (ОписаниеЗадачи) = (?) WHERE (IDзадачи) = (?)';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $data['ОписаниеЗадачи']);
        $stmt->bindValue(2, $id);
        $stmt->executeQuery();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/task/{id}", methods={"GET"})
     */
    public function fetchTask(Connection $connection, $id): Response
    {
        $sql = 'SELECT * FROM Задачи WHERE IDзадачи = ?';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $id);
        $result = $stmt->executeQuery();

        $task = $result->fetchAssociative();

        return $this->json($task);
    }

    /**
     * @Route("/task", methods={"GET"})
     */
    public function getTasks(Connection $connection): Response
    {
        $sql = 'SELECT * FROM Задачи';
        $stmt = $connection->prepare($sql);
        $result = $stmt->executeQuery();

        $tasks = $result->fetchAllAssociative();

        return $this->json($tasks);
    }

    /**
     * @Route("/task/{id}", methods={"DELETE"})
     */
    public function deleteTask(Connection $connection, $id): Response
    {
        $sql = 'DELETE FROM Задачи WHERE IDзадачи = ?';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->executeQuery();

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}