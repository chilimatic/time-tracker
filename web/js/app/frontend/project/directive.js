"use strict";
define(['app'], function(app) {
    app
        .directive('taskList', function() {
        return {
            restrict : 'E',
            scope : {
                list : "=",
                dropTarget : '='
            },
            templateUrl: '/js/app/frontend/partial/task-list.html',
            link : function(scope, element, attr)
            {

                scope.close = function(selectedId)
                {
                    for (var i in scope.list) {
                        if (scope.list[i].id == selectedId) {
                            delete scope.list[i];
                            return;
                        }
                    }
                }
            }
        }
    });


    app
        .directive('projectTileList', function() {
            return {
                restrict : 'E',
                scope : {
                    projectList : '='
                },
                templateUrl : '/js/app/frontend/project/project-tile.html',
                controller : ['$scope', '$location', 'project', function($scope, $location, project)
                {
                    $scope.menuList = [
                        {
                            'id' : 0,
                            'name' : 'details',
                            'callback' : function(entry) {
                                console.log(entry)
                            },
                            'className' : 'glyphicon glyphicon-info-sign'
                        },
                        {
                            'id' : 1,
                            'name' : 'settings',
                            'callback' : function(entry) {
                                console.log(entry)
                            },
                            'className' : 'glyphicon glyphicon-edit'
                        },
                        {
                            'id' : 2,
                            'name' : 'share',
                            'callback' : function(entry) {
                                console.log(entry)
                            },
                            'className' : 'glyphicon glyphicon-envelope'
                        },
                        {
                            'id' : 3,
                            'name' : 'delete',
                            'callback' : function(entry) {
                                if (!entry.id) {
                                    return false;
                                }

                                project.delete(
                                    {
                                        'actionName' : 'delete'
                                    },
                                    {
                                        'projectId' : entry.id
                                    },
                                    function(promise)
                                    {
                                        if (!promise.response) {
                                            return;
                                        }

                                        if (promise.response.success)
                                        {
                                            var pL = JSON.parse(JSON.stringify($scope.projectList));
                                            $scope.projectList = [];
                                            for (var i in pL) {
                                                if (entry.id == pL[i].id) {
                                                    continue;
                                                }
                                                $scope.projectList.push(pL[i])
                                            }

                                        } else if (promise.response.error) {
                                            alert(promise.response.error.msg)
                                        }
                                    }
                                );

                                return false;
                            },
                            'className' : 'glyphicon glyphicon-remove'
                        }
                    ];


                    /**
                     *
                     * @param project
                     */
                    $scope.selectProject = function(project) {
                        $location.url(PROJECT_BASE_URL + '/' + project.name);
                    };

                    /**
                     * @param projectModel
                     * @returns {boolean}
                     */
                    $scope.deleteProject = function(projectModel)
                    {

                        if (!projectModel.id) {
                            return false;
                        }

                        project.delete(
                            {
                                'actionName' : 'delete'
                            },
                            {
                                'projectId' : projectModel.id
                            },
                            function(promise)
                            {
                                if (!promise.response) {
                                    return;
                                }

                                if (promise.response.success)
                                {
                                    var pL = JSON.parse(JSON.stringify($scope.projectList));
                                    $scope.projectList = [];
                                    for (var i in pL) {
                                        if (projectModel.id == pL[i].id) {
                                            continue;
                                        }
                                        $scope.projectList.push(pL[i])
                                    }

                                }
                            }
                        );

                        return false;
                    };
                }]
            }
        });


    app.
        directive('projectForm', function() {
            return {
                restrict : 'E',
                templateUrl : '/js/app/frontend/project/project-form.html',
                controller : ['$scope', 'project', function($scope, project)
                {
                    $scope.newProjectName = '';

                    $scope.addProject = function()
                    {
                        if (!$scope.newProjectName) {
                            return;
                        }

                        project.new(
                            {
                                'actionName': 'new'
                            },
                            {
                                'name' : $scope.newProjectName
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

                }]
            }
        })

});