"use strict";

define(['app'], function(app)
{
    app
        .controller('projectController',
        [
            '$scope', '$rootScope', '$location', 'project', 'login', 'projectSession',
            function($scope, $rootScope, $location, project, login, projectSession)
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


            /* Chart options */
            $scope.options = {
                chart: {
                    type: 'multiBarChart',
                    height: 450,
                    margin : {
                        top: 20,
                        right: 20,
                        bottom: 45,
                        left: 45
                    },
                    clipEdge: true,
                    //staggerLabels: true,
                    duration: 500,
                    stacked: true,
                    xAxis: {
                        axisLabel: 'Day',
                        showMaxMin: false,
                        tickFormat: function(d){
                            return d3.format(',f')(d);
                        }
                    },
                    yAxis: {
                        axisLabel: 'amount',
                        axisLabelDistance: -20,
                        tickFormat: function(d){
                            return d3.format(',.1f')(d);
                        }
                    }
                }
            };

            /**
             * @type {Array}
             */
            $scope.data = [];

            
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

                projectSession.getUserStatistic(
                    {
                        'actionName' : 'get-user-statistic'
                    },
                    null,
                    function (promise) {

                        if (!promise.timeDiff) {
                            return;
                        }

                        if (promise.timeDiff) {
                            $scope.data = promise.timeDiff;
                        } else {
                            $scope.data = [];
                        }

                        console.log(promise.timeDiff);

                        $scope.api.refresh();
                    }
                );

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
                var currentFrom = $scope.dateRange.from ? new Date($scope.dateRange.from) : null;
                var currentTill = $scope.dateRange.till ? new Date($scope.dateRange.till) : null;
                var newSessionList = [];

                $scope.selectedProject.totalSessionList.map(
                    function(element) {
                        if (!element.startTime) {
                            return false;
                        }
                        var elementStartTime = new Date(element.startTime.replace(' ', 'T'));

                        if (currentFrom && currentFrom.getTime() <= elementStartTime.getTime()) {
                            newSessionList.push(element);
                        } else if (
                            currentTill && currentTill.getTime() >= elementStartTime.getTime() &&
                            (currentFrom && currentFrom.getTime() <= elementStartTime.getTime()))
                        {
                            newSessionList.push(element);
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
                            var totalAmount = 0;
                            $scope.totalAmount = 0;
                            $scope.selectedProject.currentSession = data;
                            $scope.selectedProject.sessionList.map(function(element) {
                                if (element.id == data.id) {
                                    for (var i in data) {
                                        element[i] = data[i];
                                    }
                                }
                                totalAmount += parseFloat(element.timeDiff);
                            });
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
                            $scope.selectedProject.sessionList.push($scope.currentSession);
                            $scope.selectedProject.totalSessionList.push($scope.currentSession);
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

                        $scope.selectedProject.sessionList.map(function(element) {
                            if (element.timeDiff && element.startTime) {
                                tmpAmount += element.timeDiff;

                                var time = new Date(element.startTime.replace(' ', 'T'));
                                timestring = time.getFullYear() + '-' + time.getMonth() + '-' + time.getDate();
                                if (set[timestring] === undefined) {
                                    set[timestring] = element.timeDiff;
                                } else {
                                    set[timestring] += element.timeDiff;
                                }
                            }
                        });

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