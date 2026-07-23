<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    /**
     * Redirect with a success flash message.
     *
     * @param string $route
     * @param string $message
     * @param array<string, mixed> $parameters
     * @return RedirectResponse
     */
    public static function successRedirect(string $route, string $message, array $parameters = []): RedirectResponse
    {
        return redirect()->route($route, $parameters)->with('success', $message);
    }

    /**
     * Redirect back or to a route with an error flash message and old input.
     *
     * @param string $route
     * @param string $message
     * @param array<string, mixed> $parameters
     * @return RedirectResponse
     */
    public static function errorRedirect(string $route, string $message, array $parameters = []): RedirectResponse
    {
        return redirect()->route($route, $parameters)->with('error', $message)->withInput();
    }

    /**
     * Return a standardized success JSON response.
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public static function successJson(mixed $data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Return a standardized error JSON response.
     *
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public static function errorJson(string $message = 'Error', int $code = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $code);
    }
}
