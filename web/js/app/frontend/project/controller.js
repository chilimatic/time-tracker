"use strict";

define(['app'], function(app)
{
    var PROJECT_BASE_URL = '/project';

    app
        .controller('projectController',
        [
            '$scope', '$rootScope', '$location', 'project', 'login',
            function($scope, $rootScope, $location, project, login)
        {
            /**
             *
             * @type {Array}
             */
            $scope.projectList = [];

            /**
             *
             * @type {string}
             */
            $scope.projectName = '';

            /**
             *
             * @type {boolean}
             */
            $scope.showAddProjectForm = false;

            /**
             *
             * @param project
             */
            $scope.selectProject = function(project){
                $location.url(PROJECT_BASE_URL + '/' + project.name);
            };

            $rootScope.$on('login-error', function(event, param1) {
                console.log(param1);
            });

            $scope.deleteProject = function(projectModel)
            {
                if (!projectModel.id) {
                    return;
                }

                project.delete(
                    {
                        'actionName' : 'delete'
                    },
                    {
                        'projectId' : projectModel.id
                    },
                    function(promise) {
                        if (!promise.response) {
                            return;
                        }

                        if (promise.reponse.success) {
                            delete $scope.projectList[$scope.projectList.indexOf(project)];
                        }
                    }
                )
            };


            $scope.addProject = function() {
                if (!$scope.projectName) {
                    return;
                }

                project.new(
                    {
                        'actionName': 'new'
                    },
                    {
                        'name' : $scope.projectName
                    },
                    function(promise) {
                        if (!promise.response) {
                            return;
                        }

                        if (!promise.response.success) {
                            return;
                        }

                        // push the last result set onto the list
                        $scope.projectList.push(promise.response.data);
                    }
                );

            };

            $scope.init = function()
            {
                login.loggedIn();

                // resume old session or start new one
                login.getSession().start();
                // get the user model
                $scope.user = login.getUserData();

                project.getList(
                    {
                        'actionName' : 'get-list'
                    },
                    function(promise) {
                        if (!promise.response) {
                            return;
                        }

                        if (promise.response.projectList) {
                            $scope.projectList = promise.response.projectList;
                        } else {
                            $scope.projectList = [];
                        }

                        if ($location.url() != PROJECT_BASE_URL) {
                            $scope.projectList.map(function(element){
                                if ($location.url().split('/').pop() == element.name) {
                                    $scope.selectProject(element);
                                }
                            });
                        }
                    }
                );
            };


            $scope.init();

        }]
    );

    app.controller('projectDetailController', [
        '$scope', '$rootScope', '$location', 'project', 'login', 'projectSession', 'task',
        function($scope, $rootScope, $location, project, login, projectSession, task)
        {
            /**
             * @type {{project: {}, sessionList: Array, currentSession: {}}}
             */
            $scope.selectedProject = {
                'project' : {},
                'sessionList' : [],
                'currentSession' : {}
            };



            /**
             *
             * @type {number}
             */
            $scope.totalAmount = 0;

            /**
             *
             * @type {{}}
             */
            $scope.taskList = {
                '58' : ['stuff', 'and', 'stuff']
            };

            /**
             *
             * @type {{}}
             */
            $scope.user = {};

            /**
             * @type {string}
             */
            $scope.timeSearch = '';

            /**
             *
             * @param sessionId
             */
            $scope.resumeSession = function (sessionId)
            {
                if ($scope.selectedProject.currentSession && $scope.selectedProject.currentSession.id == sessionId) {
                    return;
                }

                $scope.selectedProject.sessionList.map(function(element){
                   if (element.id == sessionId) {
                       $scope.selectedProject.currentSession = element;
                   }
                });

            };


            /**
             * ends the current session
             */
            $scope.endSession = function()
            {
                if (!$scope.selectedProject.currentSession) {
                    return;
                }

                projectSession.end(
                    {
                        'actionName' : 'end'
                    },
                    {
                        'sessionId' : $scope.selectedProject.currentSession.id
                    },
                    function (promise) {
                        if (!promise.response) {
                            return;
                        }

                        var data = promise.response.data;

                        if (data) {
                            $scope.totalAmount = 0;
                            $scope.selectedProject.currentSession = data;
                            $scope.selectedProject.sessionList.map(function(element) {
                                if (element.id == data.id) {
                                    for (var i in data) {
                                        element[i] = data[i];
                                    }
                                }
                                $scope.totalAmount += element.timeDiff;
                            });
                            // from minutes to hours
                            $scope.totalAmount = $scope.totalAmount / 60;
                        }
                    }
                )
            };

            /**
             *
             */
            $scope.startNewSession = function() {
                projectSession.start(
                    {
                        'actionName' : 'start'
                    },
                    {
                        'projectId' : $scope.selectedProject.project.id
                    },
                    function (promise) {
                        if (!promise.response) {
                            return;
                        }

                        var data = promise.response.data;

                        if (data) {
                            $scope.currentSession = data;
                            $scope.selectedProject.sessionList.push($scope.currentSession);
                        }
                    }

                )
            };


            $scope.getTaskList = function(projectId)
            {
                if (!projectId) {
                    return;
                }

                task.getTaskForProject(
                    {
                        'actionName' : 'get-task-for-project'
                    },
                    {
                        'project_id' : projectId
                    },
                    function(promise) {
                        if (!promise.response) {
                            return;
                        }

                        var data = promise.response.data;

                        if (data) {
                            $scope.taskList = data;
                        }
                    }
                );

            };

            $scope.init = function()
            {
                login.loggedIn();

                // resume old session or start new one
                login.getSession().start();
                // get the user model
                $scope.user = login.getUserData();

                var projectName = $location.url().split('/').pop();

                project.getDetail(
                    {
                        actionName    : 'get-detail'
                    },
                    {
                        name : projectName
                    },
                    function(promise) {
                        if (!promise) {
                            return;
                        }
                        var data = promise.response.data;

                        $scope.getTaskList(data.project.id);

                        if (data.project) {
                            $scope.selectedProject = data;

                            data.sessionList.map(function(element) {
                                if (element.timeDiff) {
                                    $scope.totalAmount += element.timeDiff;
                                }
                            });
                            // from minutes to hours
                            $scope.totalAmount = $scope.totalAmount / 60;
                        } else {
                            $scope.projectList = {
                                'project' : {},
                                'sessionList' : []
                            };
                        }

                    }
                );

            };

            $scope.init();

        }
    ])



});