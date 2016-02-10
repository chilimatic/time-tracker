"use strict";

define(['app'], function(app) {

    app
        .factory('project', ['$resource', function($resource) {
            return $resource(
                '/project/index/:actionName',
                {
                    actionName: '@id',
                    name : '@name'
                },
                {
                    'getList'   : { method: 'GET'  },
                    'new'       : { method: 'POST' },
                    'getDetail' : { method: 'GET'  },
                    'delete'    : { method: 'POST' }
                }
            );
        }]);
});