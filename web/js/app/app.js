/**
 * Created by j on 8/30/14.
 */
define(['route', 'service/resolver'], function(routing, resolver)
{
    var app = angular.module('timetracker-frontend', ['ngResource', 'ngRoute']);


    /**
     * lazy loading approach with require.js
     */
    app
        .config([
            '$routeProvider',
            '$locationProvider',
            '$controllerProvider',
            '$compileProvider',
            '$filterProvider',
            '$provide',
            '$resourceProvider',
            function($routeProvider, $locationProvider, $controllerProvider, $compileProvider, $filterProvider, $provide, $resourceProvider)
            {
                app.controller = $controllerProvider.register;
                app.directive = $compileProvider.directive;
                app.filter = $filterProvider.register;
                app.factory = $provide.factory;
                app.service = $provide.service;

                $resourceProvider.defaults.stripTrailingSlashes = false;

                $locationProvider.html5Mode({
                    enabled: true,
                    requireBase: false
                });

                if(routing.routes !== undefined)
                {
                    angular.forEach(routing.routes, function(route, path)
                    {
                        $routeProvider.when(
                            path,
                            {
                                templateUrl: route.templateUrl,
                                resolve: resolver(route.dependencies)
                            }
                        );
                    });
                }

                if(routing.defaultRoutePath !== undefined)
                {
                    $routeProvider.otherwise({redirectTo:routing.defaultRoutePath});
                }
            }
        ]);

    app
        .directive('timetrackerTopBar', function() {
            return {
                restrict : 'ACE',
                templateUrl: '/js/app/frontend/header/header-tpl.html',
                controller: function ($scope, $rootScope, $timeout) {
                }
            }
        }
    );

    app.service('user', ['$rootScope', '$window', '$resource', function($rootScope, $window, $resource)
    {

    }]);

    return app;
});