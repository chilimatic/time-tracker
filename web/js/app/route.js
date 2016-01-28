/**
 * Created by j on 8/30/14.
 */
define([], function()
{
    var PATH_TO_FRONTEND = '/js/app/frontend';

    return {
        defaultRoutePath: '/',
        routes: {
            '/': {
                dependencies: [
                    PATH_TO_FRONTEND + '/project/directive.js',
                    PATH_TO_FRONTEND + '/project/service.js',
                    PATH_TO_FRONTEND + '/project/controller.js'

                ],
                templateUrl: '/js/app/frontend/project/index.html'
            },
            '/login' : {
                dependencies: [
                    '/js/app/control/login.js'
                ],
                templateUrl: '/js/app/view/login.html'
            }
        }
    }

});
