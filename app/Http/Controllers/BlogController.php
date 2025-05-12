<?php

namespace App\Http\Controllers;

use App\Services\LoggerService;


class BlogController extends Controller
{
    public function __construct(private LoggerService $logger) {}

    public function index()
    {
        $this->logger->log("all blogs");
        return [
            [
                "title" => "blog",
            ]
        ];
    }
}
