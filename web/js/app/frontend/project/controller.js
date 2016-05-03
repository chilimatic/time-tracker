"use strict";

define(['app'], function(app)
{
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
             * @type {boolean}
             */
            $scope.showAddProjectForm = false;

            /**
             * only objects can 2 way bound
             *
             * @type {{listView: boolean}}
             */
            $scope.setting = {
                listView : false
            };
            
            $rootScope.$on('login-error', function(event, param1) {
                //console.log(param1);
            });


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
        '$scope', '$rootScope', '$location', '$timeout', 'project', 'login', 'projectSession', 'task',
        function($scope, $rootScope, $location, $timeout, project, login, projectSession, task)
        {
            /**
             * @type {{project: {}, sessionList: Array, currentSession: {}}}
             */
            $scope.selectedProject = {
                'project'           : {},
                'totalSessionList'  : [],
                'sessionList'       : [],
                'currentSession'    : {}
            };

            /**
             * @type {{from: null, till: null}}
             */
            $scope.dateRange = {
                from : null,
                till : null
            };

            $scope.setting = {
                showTask : true
            };

            /**
             * @type {string}
             */
            $scope.taskName = '';

            /**
             *
             * @type {{}}
             */
            $scope.user = {};

            /**
             * @type {string}
             */
            $scope.timeSearch = '';


            $scope.timeFilter = function()
            {
                var currentFrom = $scope.dateRange.from ? new Date($scope.dateRange.from).getTime() : 0;
                var currentTill = $scope.dateRange.till ? new Date($scope.dateRange.till).getTime() : null;
                var newSessionList = [];

                Object.keys($scope.selectedProject).map(
                    function(key) {
                        if (!$scope.selectedProject[key].startTime) {
                            return false;
                        }
                        var elementStartTime = new Date($scope.selectedProject[key].startTime.replace(' ', 'T'));

                        // if there is no lower bound or the range requirement is full filled
                        if ((!currentTill || currentTill >= elementStartTime.getTime())
                            &&
                            (!currentFrom || (currentFrom <= elementStartTime.getTime())))
                        {
                            newSessionList.push($scope.selectedProject[key]);
                        }
                    }
                );

                $scope.selectedProject.sessionList = newSessionList;
                $scope.calculateTotalDisplayedHours();
            };


            /**
             *
             */
            $scope.addTask = function()
            {
                task.create(
                    {
                        'actionName' : 'create-task'
                    },
                    {
                        'task-name'  : $scope.taskName,
                        'project-id' : $scope.selectedProject.project.id
                    },
                    function (promise) {
                        if (!promise.response) {
                            return;
                        }

                        var task = promise.response.data;

                        if ($scope.taskList[$scope.selectedProject.project.id]) {
                            $scope.taskList[$scope.selectedProject.project.id].push(task);
                        } else {
                            $scope.taskList[$scope.selectedProject.project.id] = [];
                            $scope.taskList[$scope.selectedProject.project.id].push(task);
                        }


                    }
                )
            };


            /**
             *
             * @param sessionId
             */
            $scope.resumeSession = function (sessionId)
            {
                if ($scope.selectedProject.currentSession && $scope.selectedProject.currentSession.id == sessionId) {
                    return;
                }

                var currentElement;
                for (var i in $scope.selectedProject.sessionList) {
                    currentElement = $scope.selectedProject.sessionList[i];
                    if (currentElement.id == sessionId) {
                        $scope.selectedProject.currentSession = currentElement;
                        break;
                    }
                }
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
                        'session' : $scope.selectedProject.currentSession
                    },
                    function (promise) {
                        if (!promise.response) {
                            return;
                        }

                        var data = promise.response.data;

                        if (data) {
                            var totalAmount = 0;
                            var currentElement;
                            $scope.totalAmount = 0;
                            $scope.selectedProject.currentSession = data;

                            for (var i in $scope.selectedProject.sessionList) {
                                if ($scope.selectedProject.sessionList[i].id == data.id) {
                                    $scope.selectedProject.sessionList[i] = data;
                                }
                                totalAmount += parseFloat($scope.selectedProject.sessionList[i].timeDiff);
                            }

                            // from minutes to hours
                            totalAmount = totalAmount / 60;
                            $scope.totalAmount = totalAmount.toPrecision(2);

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
                            $scope.selectedProject.sessionList[$scope.currentSession.id] = $scope.currentSession;
                            $scope.selectedProject.totalSessionList[$scope.currentSession.id] = $scope.currentSession;
                        }
                    }

                )
            };


            /**
             * @param projectId
             */
            $scope.getTaskList = function(projectId)
            {
                if (!projectId) {
                    return;
                }
                /**
                 * @todo remove after testing drag and drop
                 */
                return;

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


            $scope.calculateTotalDisplayedHours = function()
            {
                // non blocking calculation
                $timeout(
                    function() {
                        var tmpAmount = 0;
                        var set  = {};
                        var timestring ='';
                        var currentElement;

                        for (var i in $scope.selectedProject.sessionList) {
                            currentElement = $scope.selectedProject.sessionList[i];

                            if (currentElement.timeDiff && currentElement.startTime) {
                                tmpAmount += currentElement.timeDiff;

                                var time = new Date(currentElement.startTime.replace(' ', 'T'));
                                timestring = time.getFullYear() + '-' + time.getMonth() + '-' + time.getDate();
                                if (set[timestring] === undefined) {
                                    set[timestring] = currentElement.timeDiff;
                                } else {
                                    set[timestring] += currentElement.timeDiff;
                                }
                            }
                        }

                        var timeSet = [];
                        for (var i in set) {
                            timeSet.push(i.set)
                        }

                        // from minutes to hours
                        $scope.totalAmount = Math.round(tmpAmount / 60);
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

                        if (data.project)
                        {
                            $scope.getTaskList(data.project.id);
                            $scope.selectedProject = data;
                            $scope.selectedProject.totalSessionList = data.sessionList;
                            $scope.calculateTotalDisplayedHours();
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