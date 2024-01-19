<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Connection;

class AssController extends AbstractController
{

    /**
     * @Route("/assignment", methods={"POST"})
     */
    public function createAssignment(Connection $connection, Request $request): Response
    {
        $data = $request->request->all();

        $sql = 'INSERT INTO Назначения (IDзадачи, IDпользователя) VALUES (?, ?)';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $data['IDзадачи']);
        $stmt->bindValue(2, $data['IDпользователя']);
        $stmt->executeQuery();

        return new Response('', Response::HTTP_CREATED);
    }

    /**
     * @Route("/assignment/{id}", methods={"GET"})
     */
    public function fetchAssignment(Connection $connection, $id): Response
    {
        $sql = 'SELECT * FROM Назначения WHERE IDзадачи = ?';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $id);
        $result = $stmt->executeQuery();

        $assignment = $result->fetchAssociative();

        return $this->json($assignment);
    }

    /**
     * @Route("/assignment/{id}", methods={"PUT"})
     */
    public function updateAssignment(Connection $connection, Request $request, $id): Response
    {
        $data = $request->request->all();

        $sql = 'UPDATE Назначения SET IDпользователя = ? WHERE IDзадачи = ?';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $data['IDпользователя']);
        $stmt->bindValue(2, $id);
        $stmt->executeQuery();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/assignment/{id}", methods={"DELETE"})
     */
    public function deleteAssignment(Connection $connection, $id): Response
    {
        $sql = 'DELETE FROM Назначения WHERE IDзадачи = ?';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->executeQuery();

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}