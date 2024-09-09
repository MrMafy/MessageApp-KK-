<?php

namespace App\Controllers;

use App\Models\CategoriesModel;

class HomeController
{
    private $pdo;
    private $categoriesModel;

    public function __construct()
    {
        $this->categoriesModel = new CategoriesModel($this->pdo);
    }

    public function index()
    {
        $title = "MessageApp (KK)";
        $categories = $this->categoriesModel->getCategories();

        // Использование абсолютного путя к файлу шаблона
        include __DIR__ . '/../../resources/views/main.php';
    }
}
