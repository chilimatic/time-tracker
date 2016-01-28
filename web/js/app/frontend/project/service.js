"use strict";

define(['app'], function(app) {

    app
        .factory('project', ['$resource', function($resource) {
            return $resource(
                '/project/index/:actionName/',
                {
                    actionName: '@id'
                },
                {
                    'getList': { method: 'GET' },
                    'getArticle' : { method: 'GET' }
                }
            );
        }]);
});