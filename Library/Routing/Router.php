<?php

    /*
     * Copyright (c) 2011-2012, Celestino Diaz <celestino.diaz@gmx.de>.
     * All rights reserved.
     *
     * Redistribution and use in source and binary forms, with or without
     * modification, are permitted provided that the following conditions
     * are met:
     *
     * 1. Redistributions of source code must retain the above copyright
     *    notice, this list of conditions and the following disclaimer.
     * 2. Redistributions in binary form must reproduce the above copyright
     *    notice, this list of conditions and the following disclaimer in the
     *    documentation and/or other materials provided with the distribution.
     * 3. Neither the name of Brickoo nor the names of its contributors may be used
     *    to endorse or promote products derived from this software without specific
     *    prior written permission.
     *
     * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
     * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
     * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
     * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
     * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
     * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
     * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
     * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
     * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
     * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
     * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
     */

    namespace Brickoo\Library\Routing;

    use Brickoo\Library\Core;
    use Brickoo\Library\System;
    use Brickoo\Library\Validator\TypeValidator;

    /**
     * Router
     *
     * Router class which handles the routes collected.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Router implements Interfaces\RouterInterface
    {

        /**
        * Holds the class dependencies.
        * @var array
        */
        protected $dependencies;

        /**
         * Returns the dependency holded, created or overwritten.
         * @param string $name the name of the dependency
         * @param string $interface the interface which has to be implemented by the dependency
         * @param callback $callback the callback to create a new dependency
         * @param object $Dependecy the dependecy used to overwrite
         * @return object Request if overwritten otherwise the dependency
         */
        protected function getDependency($name, $interface, $callback, $Dependecy = null)
        {
            if ($Dependecy instanceof $interface) {
                $this->dependencies[$name] = $Dependecy;
                return $this;
            }
            elseif ((! isset($this->dependencies[$name])) || (! $this->dependencies[$name] instanceof $interface)) {
                $this->dependencies[$name] = call_user_func($callback, $this);
            }
            return $this->dependencies[$name];
        }

        /**
         * Lazy initialization of the RouteCollection dependecy.
         * @param \Brickoo\Library\Routing\Interfaces\RouteCollectionInterface $RouteCollection the colection of routes
         * @return \Brickoo\Library\Routing\Interfaces\RouterCollectionInterface
         */
        public function RouteCollection(\Brickoo\Library\Routing\Interfaces\RouteCollectionInterface $RouteCollection = null)
        {
            return $this->getDependency(
                'RouteCollection',
                '\Brickoo\Library\Routing\Interfaces\RouteCollectionInterface',
                function(){return new RouteCollection();},
                $RouteCollection
            );
        }

        /**
         * Holds the routes cache file name.
         * @var string
         */
        protected $cacheFilename;

        /**
         * Returns the cache file name.
         * @return string the cache file name
         */
        public function getCacheFilename()
        {
            return $this->cacheFilename;
        }

        /**
         * Sets the cache file name.
         * @param string $cacheFilename the cache file name
         * @return \Brickoo\Library\Routing\Router
         */
        public function setCacheFilename($cacheFilename)
        {
            TypeValidator::IsString($cacheFilename);

            $this->cacheFilename = $cacheFilename;

            return $this;
        }

        /**
         * Holds the cache directory to use.
         * @var string
         */
        protected $cacheDirectory;

        /**
         * Returns the cache directory used.
         * @throws UnexpectedValueException if the cache directory is not set
         * @return string the cache directory
         */
        public function getCacheDirectory()
        {
            if ($this->cacheDirectory === null) {
                throw new \UnexpectedValueException('The cache directory is `null`.');
            }

            return $this->cacheDirectory;
        }

        /**
         * Sets the cache directory to use.
         * @param string $cacheDirectory the cache directory to use
         * @return \Brickoo\Library\Routing\Router
         */
        public function setCacheDirectory($cacheDirectory)
        {
            TypeValidator::IsString($cacheDirectory);

            $this->cacheDirectory = rtrim($cacheDirectory, '/\\') . DIRECTORY_SEPARATOR;

            return $this;
        }

        /**
         * Checks if the cache directory is set.
         * @return boolean check result
         */
        public function hasCacheDirectory()
        {
            return ($this->cacheDirectory !== null);
        }

        /**
         * Holds the routes file name search at the modules.
         * @var string
         */
        protected $routesFilename;

        /**
         * Returns the routes file name.
         * @return string the routes file name
         */
        public function getRoutesFilename()
        {
            return $this->routesFilename;
        }

        /**
         * Sets the  routes file name searched in the modules directory.
         * @param string $routesFilename the routes file name
         * @return \Brickoo\Library\Routing\Router
         */
        public function setRoutesFilename($routesFilename)
        {
            TypeValidator::IsString($routesFilename);

            $this->routesFilename = $routesFilename;

            return $this;
        }

        /**
         * Holds the modules to analyze for application routes.
         * @var array
         */
        protected $modules;

        /**
         * Returns the modules available.
         * @return array the modules available
         */
        public function getModules()
        {
            return $this->modules;
        }

        /**
         * Sets the modules to load the routes from if available.
         * If the modules a set directly, the modules will not be available through the Brickoo Registry.
         * @param array $modules the modules to load the routes from
         * @throws Core\Exceptions\ValueOverwriteException if trying to overwrite the available modules
         * @return \Brickoo\Library\Routing\Router
         */
        public function setModules(array $modules)
        {
            if (! empty($this->modules)) {
                throw new Core\Exceptions\ValueOverwriteException('Router::modules');
            }

            $this->modules = $modules;

            return $this;
        }

        /**
         * Checks if any modules are available.
         * @return boolean check result
         */
        public function hasModules()
        {
            return (! empty($this->modules));
        }

        /**
         * Holds an instance of the Request class.
         * @var \Brickoo\Library\Core\Interfaces\DynamicRequestInterface
         */
        protected $Request;

        /**
         * Returns the Request instance implementing the DynamicRequestInterface.
         * @return \Brickoo\Library\Core\Interfaces\DynamicRequestInterface
         */
        public function getRequest()
        {
            return $this->Request;
        }

        /**
         * Holds the requeste Route instance.
         * @var Brickoo\Library\Routing\Interfaces\Route
         */
        protected $RequestRoute;

        /**
         * Sets the requested Route for further routing.
         * @param \Brickoo\Library\Routing\Interfaces\RouteInterface $Route the route matched the request
         * @throws \Brickoo\Library\Core\Exceptions\ValueOverwriteException if trying to overwrite the request route
         * @return \Brickoo\Library\Routing\Router
         */
        public function setRequestRoute(\Brickoo\Library\Routing\Interfaces\RouteInterface $Route)
        {
            if ($this->RequestRoute !== null) {
                throw new Core\Exceptions\ValueOverwriteException('Router::RequestRoute');
            }

            $this->RequestRoute = $Route;

            return $this;
        }

        /**
         * Checks if the requested Route has been found and is set.
         * @return boolean check result
         */
        public function hasRequestRoute()
        {
            return ($this->RequestRoute instanceof Interfaces\RouteInterface);
        }

        /**
         * Checks if the Route matches the request.
         * @return boolean check result
         */
        public function isRequestRoute(\Brickoo\Library\Routing\Interfaces\RouteInterface $Route)
        {
            $Request = $this->getRequest();

            return(
                preg_match('~^(' . $Route->getMethod() . ')$~i', $Request->getMethod()) &&
                (
                    (($hostname = $Route->getHostname()) === null) ||
                    preg_match('~^(' . $hostname . ')$~i', $Request->getHost())
                ) &&
                preg_match($this->getRegexFromRoutePath($Route), $Request->getPath())
            );
        }

        /**
         * Checks if the cached route matches the request.
         * @param array $route the route configuration to check
         * @return boolean check result
         */
        public function isCachedRequestRoute(array $route)
        {
            TypeValidator::ArrayContainsKeys(array('method', 'path', 'hostname', 'class'), $route);

            $Request = $this->getRequest();

            return(
                preg_match($route['method'], $Request->getMethod()) &&
                (
                    ($route['hostname'] === null) ||
                    preg_match($route['hostname'], $Request->getHost())
                ) &&
                preg_match($route['path'], $Request->getPath())
            );
        }

        /**
         * Returns the request matching route.
         * If the CacheManager is available the proceded routes will be cached.
         * @throws Routing\Exceptions\RequestedHasNoRouteException if the request has not a matching Route
         * @return \Brickoo\Library\Routing\Route
         */
        public function getRequestRoute()
        {
            if ($this->hasRequestRoute()) {
                return $this->RequestRoute;
            }

            if (! $this->RouteCollection()->hasRoutes()) {
                $this->collectModulesRoutes();
            }

            if ($routes = $this->RouteCollection()->getRoutes()) {
                foreach($routes as $Route) {
                    if ($this->isRequestRoute($Route)) {
                        $this->setRequestRoute($Route);
                        break;
                    }
                }
            }

            if (! $this->hasRequestRoute()) {
                throw new Exceptions\RequestHasNoRouteException($this->getRequest()->getPath());
            }

            $this->saveRoutesToCache();

            return $this->getRequestRoute();
        }

        /**
        * Class constructor.
        * Injects a Request dependency implementing the Core}Interfaces\DynamicRequestInterface
        * Initializes the class properties.
        * @return void
        */
        public function __construct(\Brickoo\Library\Core\Interfaces\DynamicRequestInterface $Request)
        {
            $this->Request            = $Request;
            $this->RequestRoute       = null;
            $this->dependencies       = array();
            $this->FileObject         = null;
            $this->cacheDirectory     = null;
            $this->modules            = array();
            $this->cacheFilename      = 'router.routes.php';
            $this->routesFilename     = 'routes.php';
        }

        /**
         * Collectes the routes available to add to the RouteCollection.
         * Searches through all available modules available to require the route collections.
         * This requires the registered modules, which is normaly done by the FrontController.
         * @return void
         */
        public function collectModulesRoutes()
        {
            if ($modules = $this->getModules()) {
                foreach($modules as $modulePath) {
                    if
                    (
                        file_exists(($routingFilename = $modulePath . $this->getRoutesFilename()))&&
                        ($ModuleRouteCollection = (require ($routingFilename))) &&
                        ($ModuleRouteCollection instanceof Interfaces\RouteCollectionInterface) &&
                        $ModuleRouteCollection->hasRoutes()
                    ) {
                        $this->RouteCollection()->addRoutes($ModuleRouteCollection->getRoutes());
                    }
                }
            }
        }

        /**
         * Returns the parsed routes for caching purpose.
         * @return array the parsed routes
         */
        public function getCompressedRoutes()
        {
            $parsedRoutes = array();

            if ($routes = $this->RouteCollection()->getRoutes()) {
                foreach ($routes as $Route) {
                    $routeConfig = array(
                        'method'      => '~^(' . $Route->getMethod() . ')$~i',
                        'path'        => $this->getRegexFromRoutePath($Route),
                        'hostname'    => ($Route->getHostname() ? '~^(' . $Route->getHostname() . ')$~i' : null),
                        'class'       => serialize($Route),
                    );

                    $parsedRoutes[] = $routeConfig;
                }
            }

            return $parsedRoutes;
        }

        /**
         * Loads the routes from the cache file and tries to find the request matching route.
         * This requires an available cache directory with read permission.
         * @return void
         */
        public function loadRoutesFromCache()
        {
            if (file_exists(($filename = $this->getCacheDirectory() . $this->getCacheFilename())) &&
                is_readable($filename) &&
                is_array(($cachedRoutes = include ($filename)))
            ) {
                foreach ($cachedRoutes as $cachedRoute) {
                    if ($this->isCachedRequestRoute($cachedRoute) &&
                        (($Route = unserialize($cachedRoute['class'])) instanceof Interfaces\RouteInterface)
                    ) {
                        $this->setRequestRoute($Route);
                        break;
                    }
                }
            }
        }

        /**
         * Saves the parsed routes to the cache directory.
         * This requires an available cache directory with write permission.
         * @return void
         */
        public function saveRoutesToCache()
        {
            if($routes = $this->getCompressedRoutes()) {
                if (! is_writeable(($directory = $this->getCacheDirectory()))) {
                    throw new System\Exceptions\DirectoryIsNotWriteableException($directory);
                }

                file_put_contents(
                   $directory . $this->getCacheFilename(),
                    "<?php \nreturn ". var_export($routes, true) . "; ?>"
                );
            }
        }

        /**
         * Returns a regular expression from the route path and rules or default values available.
         * @param \Brickoo\Library\Routing\Interfaces\RouteInterface $Route the route to use
         * @return string the regular expresion for the request path
         */
        public function getRegexFromRoutePath(\Brickoo\Library\Routing\Interfaces\RouteInterface $Route)
        {
            if (preg_match_all('~(\{(?<parameters>[\w]+)\})~', ($regex = $Route->getPath()), $matches)) {
                foreach ($matches['parameters'] as $parameterName) {
                    if ($Route->hasRule($parameterName)) {
                        $regex = str_replace(
                            '/{' . $parameterName . '}',
                            (
                                $Route->hasDefaultValue($parameterName) ?
                                '(/(?<' . $parameterName .'>(' . $Route->getRule($parameterName) . ')?))?' :
                                '/(?<'. $parameterName . '>' . $Route->getRule($parameterName) . ')'
                            ),
                            $regex
                        );
                        continue;
                    }

                    $regex = str_replace('{' . $parameterName . '}', uniqid($parameterName), $regex);
                }
            }

            return '~^/' . trim($regex, '/') . '$~i';
        }

    }