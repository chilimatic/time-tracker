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
             * @type {String}
             */
            $scope.filterTerm = '';

            /**
             * only objects can 2 way bound
             *
             * @type {{listView: boolean}}
             */
            $scope.setting = {
                listView : true
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
                project           : {},
                totalSessionMap   : {},
                sessionList       : [],
                currentSession    : {
                    id                  : null,
                    user_id             : null,
                    project_id          : null,
                    timeDiff            : 0,
                    done                : 0,
                    sessionDescription  : '',
                    startTime           : '',
                    endTime             : ''
                }
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
                var sessionMap = $scope.selectedProject.totalSessionMap;

                Object.keys(sessionMap).map(
                    function(key) {
                        if (!sessionMap[key].startTime) {
                            return false;
                        }
                        let elementStartTime = new Date(sessionMap[key].startTime.replace(' ', 'T'));

                        // if there is no lower bound or the range requirement is full filled
                        if ((!currentTill || currentTill >= elementStartTime.getTime())
                            &&
                            (!currentFrom || (currentFrom <= elementStartTime.getTime())))
                        {
                            newSessionList.push(sessionMap[key]);
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


            $scope.saveSession = function(session) {
                projectSession.save(
                    {
                        'actionName' : 'saveSession'
                    },
                    {
                        'session' : session
                    },
                    function (promise) {
                        if (!promise.response) {
                            return;
                        }

                        let data = promise.response.data;

                        if (data) {
                            $scope.processSavedSession(data);
                        }
                    }
                )
            };

            $scope.processSavedSession = function(data) {
                var totalAmount = 0;
                $scope.totalAmount = 0;
                $scope.selectedProject.currentSession = data;
                let sessionList = $scope.selectedProject.sessionList.map(function(session){
                    if (session.id == data.id) {
                        return data;
                    }

                    totalAmount += parseFloat(session.timeDiff);
                    return session;
                });

                // from minutes to hours
                totalAmount = totalAmount / 60;
                $scope.totalAmount = totalAmount.toPrecision(2);
                $scope.selectedProject.sessionList = sessionList;
            };


            /**
             * ends the current session
             */
            $scope.endSession = function(session)
            {
                if (!session || !session.id) {
                    return;
                }


                projectSession.end(
                    {
                        'actionName' : 'end'
                    },
                    {
                        'session' : session
                    },
                    function (promise) {
                        if (!promise.response) {
                            return;
                        }

                        let data = promise.response.data;

                        if (data) {
                            $scope.processSavedSession(data);
                        }

                    }
                )
            };

            /**
             *
             */
            $scope.startNewSession = function()
            {
                var selectedProject = $scope.selectedProject.project;
                projectSession.start(
                    {
                        'actionName' : 'start'
                    },
                    {
                        'projectId' : selectedProject.id
                    },
                    function (promise) {
                        if (!promise.response) {
                            return;
                        }

                        let data = promise.response.data;

                        if (data) {
                            $scope.currentSession = data;
                            $scope.selectedProject.sessionList.unshift(data);
                            $scope.selectedProject.totalSessionMap[$scope.currentSession.id] = data;
                        }
                    }

                )
            };

            $scope.deleteSession = function(session) {

                projectSession.deleteSession(
                    {
                        'actionName' : 'delete'
                    },
                    {
                        'session' : session
                    },
                    function(promise) {
                        if (!promise.response) {
                            return;
                        }

                        var data = promise.response.data;
                        let sessionList = $scope.selectedProject.sessionList;
                        sessionList = sessionList.filter(function(session) {
                            if (session.id == data.session_id) {
                                return false;
                            }
                            return true;
                        });

                        $scope.selectedProject.sessionList = sessionList;
                    }
                );

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
                        let tmpAmount = 0;
                        let timeSet  = {};
                        let timeString ='';
                        let currentElement;
                        let time;

                        for (let i in $scope.selectedProject.sessionList) {
                            currentElement = $scope.selectedProject.sessionList[i];

                            if (currentElement.timeDiff && currentElement.startTime) {
                                tmpAmount += currentElement.timeDiff;

                                time = new Date(currentElement.startTime.replace(' ', 'T'));
                                timeString = time.getFullYear() + '-' + time.getMonth() + '-' + time.getDate();
                                if (timeSet[timeString] === undefined) {
                                    timeSet[timeString] = currentElement.timeDiff;
                                } else {
                                    timeSet[timeString] += currentElement.timeDiff;
                                }
                            }
                        }

                        let newtimeSet = [];
                        for (var i in timeSet) {
                            newtimeSet.push(timeSet[i])
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

                let projectName = $location.url().split('/').pop();
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
                        let data = promise.response.data;

                        if (data.project)
                        {


                            let sessionList = Object.keys(data.sessionList)
                                .map(function(index) {
                                    if (!data.sessionList[index].sessionDescription) {
                                        data.sessionList[index].sessionDescription = {};
                                    }

                                    return data.sessionList[index];
                                })
                                .sort(function(sessionA, sessionB){
                                    if(sessionA.startTime > sessionB.startTime) {
                                        return -1;
                                    } else if (sessionA.startTime < sessionB.startTime) {
                                        return 1;
                                    }

                                    return 0;
                                });

                            var sessionMap = {};

                            sessionList.map((session) => sessionMap[session.id] = session);

                            $scope.getTaskList(data.project.id);
                            $scope.selectedProject.project = data.project;
                            $scope.selectedProject.totalSessionMap = sessionMap;
                            $scope.selectedProject.sessionList = sessionList;
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