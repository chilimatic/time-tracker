"use strict";
define(['app'], function(app) {
    app
        .directive('taskList', function() {
        return {
            restrict : 'E',
            scope : {
                list : "="
            },
            template: '<div class="col-xs-4 task-list"><span class="task tag" ng-repeat="task in list" ng-if="task.name">{{task.name}}<a class="glyphicon glyphicon-remove" ng-click="close(task.id)"></a></span></div>',
            link : function(scope, element, attr) {
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
        .directive('sessionList', function() {

        })

});