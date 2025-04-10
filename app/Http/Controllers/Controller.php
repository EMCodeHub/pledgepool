<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Annotations as OA;  // Import OpenApi\Annotations

/**
 * @OA\Tag(
 *     name="Controller",
 *     description="Base controller for handling common logic"
 * )
 */

/**
 * @OA\Info(
 *     title="PledgePool API",
 *     description="API for PledgePool application",
 *     version="1.0.0",
 *     @OA\Contact(
 *         email="support@pledgepool.com"
 *     )
 * )
 */

/**
 * Class Controller
 *
 * @OA\Info(
 *     title="Base Controller",
 *     description="Controller for basic functionality, such as validation and authorization.",
 *     version="1.0.0"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @OA\Get(
     *     path="/api/healthcheck",
     *     summary="Health check for the API",
     *     description="Simple endpoint to check if the API is working",
     *     tags={"Controller"},
     *     @OA\Response(
     *         response=200,
     *         description="API is working",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
}
