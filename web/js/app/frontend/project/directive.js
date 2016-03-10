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
                link : function(scope, element, attr) {

                }
            }
        })

});