<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Attributes as OA;


#[OA\OpenApi(
    info: new OA\Info(
        version: "1.0.0",
        title: "What to watch API"
    ),
    paths: [new OA\PathItem('/api')]
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctumAuth',
    type: 'http',
    description: "Bearer token for authentication",
    name: "Authorization",
    in: "header",
    scheme: 'bearer'
)]
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
