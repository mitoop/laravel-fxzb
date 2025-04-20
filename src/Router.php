<?php

namespace Mitoop\Fxzb;

use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Throwable;

class Router
{
    protected array $collections = [];

    protected array $routes = [];

    public function register(array $routes): void
    {
        $this->routes = $routes;

        foreach ($routes as $group => $groupRoutes) {
            $collection = new RouteCollection;

            foreach ($groupRoutes as $pattern => $conf) {
                $route = new Route($pattern, methods: ['POST']);
                $collection->add($pattern, $route);
            }

            $this->collections[$group] = $collection;
        }
    }

    public function match(Service $service, string $uri): ?array
    {
        if (! isset($this->collections[$service->value])) {
            return null;
        }

        try {
            $context = new RequestContext;
            $context->setMethod('POST');

            $matcher = new UrlMatcher($this->collections[$service->value], $context);
            $match = $matcher->match($uri);

            if ($route = $this->routes[$service->value][$match['_route']] ?? null) {
                $path = $route['path'];

                foreach ($match as $key => $value) {
                    if ($key === '_route') {
                        continue;
                    }

                    $path = str_replace("{{$key}}", $value, $path);
                }

                $route['real_path'] = $path;

                return $route;
            }

            return null;
        } catch (Throwable) {
            return null;
        }
    }
}
