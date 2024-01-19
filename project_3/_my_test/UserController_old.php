//<?php

// namespace App\Controller;

// use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\Routing\Annotation\Route;
// use Doctrine\DBAL\Connection;

// class UserController extends AbstractController
// {
//     /**
//      * @Route("/user", methods={"GET"})
//      */
//     public function getUsers(Connection $connection): Response
//     {
//         // Запрос SQL для получения всех строк из таблицы "Пользователи"
//         $sql = 'SELECT * FROM Пользователи';
//         $stmt = $connection->prepare($sql);
//         $result = $stmt->executeQuery();

//         // Получаем результаты запроса
//         $users = $result->fetchAllAssociative();

//         // Возвращаем пользователей в формате JSON
//         return $this->json($users);
//     }
// }


