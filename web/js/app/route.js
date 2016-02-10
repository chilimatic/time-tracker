/**
 * Created by j on 8/30/14.
 */
define([], function()
{
    var PATH_TO_FRONTEND = '/js/app/frontend';

    return {
        defaultRoutePath: '/login',
        routes: {
            '/login' : {
                dependencies: [
                    PATH_TO_FRONTEND + '/login/directive.js',
                    PATH_TO_FRONTEND + '/login/service.js',
                    PATH_TO_FRONTEND + '/login/controller.js'
                ],
                templateUrl: PATH_TO_FRONTEND + '/login/index.html'
            },
            '/project' : {
                dependencies: [
                    PATH_TO_FRONTEND + '/project/directive.js',
                    PATH_TO_FRONTEND + '/project/service.js',
                    PATH_TO_FRONTEND + '/project/controller.js'
                ],
                templateUrl: '/js/app/frontend/project/index.html'
            },
            '/project/:projectName' : {
                dependencies: [
                    PATH_TO_FRONTEND + '/project/directive.js',
                    PATH_TO_FRONTEND + '/project/service.js',
                    PATH_TO_FRONTEND + '/project/controller.js'
                ],
                templateUrl: '/js/app/frontend/project/project-detail.html'
            }
        }
    }

});
