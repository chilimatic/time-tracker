<?php
/**
 *
 * @author j
 * Date: 2/9/15
 * Time: 11:28 PM
 *
 * File: usersettings.class.php
 */
namespace timetracker\app\module\user\controller;
use timetracker\app\module\main\controller\Application;
use chilimatic\lib\view\AbstractView;

/**
 * Class UserSettings
 *
 * @package timetracker\app\module\user\controller
 */
class UserSettings extends Application
{
    /**
     * @param AbstractView $view
     */
    public function __construct(AbstractView $view = null){
        $this->loadUserFromSession();
        parent::__construct($view);
    }

    /**
     * @throws \ErrorException
     */
    public function indexAction()
    {
        $em = \chilimatic\lib\di\ClosureFactory::getInstance()->get('entity-manager');
        $phtml = new \chilimatic\lib\view\PHtml();



        $this->getView()->response = [
            'data' => [
                'title' => 'Settings',
                'jsController' => 'SettingManager',
                'content' => $phtml->render(\chilimatic\lib\config\Config::get('document_root') . '/app/view/admin/user/settings.phtml')
            ],
            'call' => 'initWindow'
        ];
    }
}