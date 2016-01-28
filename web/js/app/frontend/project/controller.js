"use strict";

define(['app'], function(app)
{
    app
        .controller('projectController', ['$scope', '$rootScope', 'user' , function($scope, $rootScope, user)
        {
            $scope.test = 12;
        }]
    );
});