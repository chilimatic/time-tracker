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
            templateUrl: '/js/app/frontend/header/header-tpl.html',
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
        .directive('sessionList', function() {

        })

});