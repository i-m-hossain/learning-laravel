<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
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
