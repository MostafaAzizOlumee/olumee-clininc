<?php

function getCurrentRoute(): string
{
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    return basename($uri);
}

function isThisRoute(string $route): bool
{
    return getCurrentRoute() === $route;
}