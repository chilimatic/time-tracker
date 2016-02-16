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
                controller: ['$scope', '$rootScope', 'login', function ($scope, $rootScope, login)
                {
                    $rootScope.$on('loggedIn', function() {
                        $scope.userData = login.getUserData();
                    });
                    $rootScope.$on('loggedOut', function() {
                        $scope.userData = null;
                    });

                    $scope.logout = function() {
                        login.logOut();
                    }
                }]
            }
        }
    );

    app.service('user', ['$resource', function($resource){
        return $resource(
            '/user/:controllerName/:actionName/:id',
            null,
            {
                'getList'   : { method: 'GET' },
                'get'       : { method: 'GET' },
                'create'    : { method: 'POST'},
                'login'     : { method: 'POST' },
                'loggedIn'  : { method: 'GET' },
                'logout'    : { method: 'POST'}
            }
        );
    }]);


    app.service('sessionStorage', function() {
        return window.sessionStorage;
    });


    /**
     * login service
     */
    app.service('login', ['$rootScope', '$location', 'session', 'user', 'sessionStorage', function($rootScope, $location, session, user, sessionStorage)
    {
        var login = {
            /**
             * userData
             */
            userData : null,
            /**
             * user service
             */
            user: null,
            /**
             * session service
             */
            session : null,

            /**
             * method that checks if a user is logged in on the server side
             */
            loggedIn : function ()
            {
                var that = this;
                this.getUser().loggedIn(
                    {
                        controllerName  : 'user',
                        actionName      : 'check-valid-session'
                    },
                    null,
                    function(promise) {
                        if (promise.response.success) {

                            if (promise.response.data) {
                                if (promise.response.data.logout == true) {
                                    that.logOut();
                                    $location.url('/login');
                                }
                            } else {
                                if (login.getUserData() ) {
                                    that.getSession().start();
                                    if ($location.url() == '/login') {
                                        $location.url('/project');
                                    }
                                }
                            }
                        } else {
                            that.logOut();
                            $location.url('/login');
                        }
                    }.bind(that)
                );
            },

            /**
             * @param username
             * @param password
             */
            logIn : function(username, password)
            {
                if (this.getSession().loggedIn) {
                    return true;
                }

                var that = this;
                this.getUser().login(
                    {
                        controllerName    : 'user',
                        actionName        : 'login'
                    },
                    {
                        username    : username,
                        password    : password
                    }
                    ,function(promise) {
                        if (!promise.response) {
                            return;
                        }

                        if (promise.response.error) {
                            that.logOut();
                            return;
                        }

                        if (promise.response.user)
                        {
                            that.setUserData(promise.response.user);
                            that.getSession().start();
                            // store them in the session
                            sessionStorage.setItem('userData', JSON.stringify(promise.response.user));
                            $rootScope.$emit('loggedIn');
                            $location.url('/project');
                        }

                    }.bind(that)
                );
            },
            /**
             * logout wrapper
             */
            logOut : function () {
                if (!this.getSession().loggedIn) {
                    return;
                }

                var that = this;
                this.user.logout(
                    {
                        controllerName    : 'user',
                        actionName        : 'logout'
                    },
                    null
                    ,function(promise) {
                        if (!promise.response.success) {
                            return;
                        }
                        that.setUserData(null);
                        that.getSession().destroy();
                        that.destroyLocalSessionData();
                        $rootScope.$emit('loggedOut');
                        $location.url('/login');
                    }.bind(that)
                );
            },

            /**
             * @param session
             */
            setSession : function(session) {
                this.session = session;
            },

            /**
             * @returns {null}
             */
            getSession : function() {
                return this.session;
            },

            /**
             * @param user
             */
            setUser : function(user) {
                this.user = user;
            },

            /**
             * @returns {null}
             */
            getUser : function () {
                return this.user;
            },
            /**
             * loads the userData from the session
             */
            getUserFromSession : function () {
                var tmpData = sessionStorage.getItem('userData');

                if (!tmpData) {
                    return null;
                }

                return JSON.parse(sessionStorage.getItem('userData'));
            },

            /**
             * destroys the session data in the session storage
             */
            destroyLocalSessionData : function () {
                sessionStorage.removeItem('userData');
            },
            /**
             *
             * @param userData
             */
            setUserData : function(userData) {
                this.userData = userData;
            },
            /**
             *
             * @returns {null}
             */
            getUserData : function () {
                if (!this.userData) {
                    this.userData = this.getUserFromSession();
                }
                return this.userData;
            }
        };

        // assign the session object
        login.setSession(session);
        login.setUser(user);

        return login;
    }]);


    app.service('session', ['sessionStorage', function(sessionStorage)
    {
        return {
            /**
             * boolean indicator if user is logged in or not
             */
            loggedIn    : false,
            /**
             * starting time of the session
             */
            startTime   : null,

            /**
             * end time of the session
             */
            endTime     : null,

            /**
             * resets all the session data
             */
            destroy : function() {
                this.loggedIn = false;
                this.endTime = null;
                this.startTime = null;
                sessionStorage.removeItem('sessionData');
            },

            /**
             * @returns {boolean}
             */
            start : function ()
            {
                if (this.startTime) {
                    return false;
                } else if (this.restore()) {
                    return true;
                }

                this.loggedIn = true;
                this.startTime = new Date();
                this.endTime = null;
                this.store();

                return true;
            },

            /**
             * ends the active session
             * @returns {boolean}
             */
            end : function()
            {
                this.loggedIn = false;
                if (!this.startTime) {
                    return false;
                }

                this.endTime = new Date();
                this.store();
                return true;
            },
            /**
             * stores the sessionData
             */
            store : function () {
                sessionStorage.setItem('sessionData', JSON.stringify(this));
            },
            /**
             * gets an old active session from the sessionStorage
             */
            restore : function() {
                var tmp = sessionStorage.getItem('sessionData');
                if (tmp != "undefined" && tmp) {
                    return this.fill(JSON.parse(tmp));
                }

                return false;
            },

            /**
             * fills the data from the storage
             * @param data
             */
            fill : function(data)
            {
                if (!data) {
                    return false;
                }

                if (data.startTime) {
                    this.startTime = new Date(data.startTime);
                }
                if (data.endTime) {
                    this.endTime = new Date(data.endTime);
                }
                if (data.loggedIn) {
                    this.loggedIn = data.loggedIn;
                }

                return true;
            }
        };
    }]);

    app.service('projectSession', ['$resource', function($resource){
        return $resource(
            '/session/index/:actionName',
            null,
            {
                'getList'   : { method: 'GET' },
                'get'       : { method: 'GET' },
                'start'     : { method: 'POST'},
                'end'       : { method: 'POST'}
            }
        );
    }]);


    app
        .factory('task', ['$resource', function($resource) {
            return $resource(
                '/project/task/:actionName',
                {
                    actionName: '@id',
                },
                {
                    'getTaskForProject': {method : 'POST'},
                    'getList'   : { method: 'GET'  },
                    'create'       : { method: 'POST' },
                    'delete'    : { method: 'POST' }
                }
            );
        }]);


    return app;
});