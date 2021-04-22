<?php

namespace Liip\User;

use Liip\User\Classes\AuthManager;
use Liip\User\Models\UserRole;
use RainLab\User\Controllers\Users;
use RainLab\User\Models\User;
use System\Classes\PluginBase;
use App;
use Event;
use Backend;

/**
 * user Plugin Information File
 */
class Plugin extends PluginBase
{
    public $require = [
        'RainLab.User',
    ];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'User',
            'description' => 'expose rainlab user as rest api',
            'author'      => 'liip',
            'icon'        => 'icon-leaf'
        ];
    }

    public function registerMailTemplates()
    {
        return [
            'liip.user::mail.register',
            'liip.user::mail.restore'
        ];
    }

    public function boot()
    {
        App::singleton('user.auth', function () {
            return AuthManager::instance();
        });
        $this->extendRainlabUserSideMenu();
        $this->extendRainlabUserUserModel();
        $this->extendRainlabUserFormFields();
    }

    /**
     * Extend Rainlab.User side menu.
     *
     * @return void
     */
    protected function extendRainlabUserSideMenu()
    {
        Event::listen('backend.menu.extendItems', function ($manager) {
            $manager->addSideMenuItems('RainLab.User', 'user', [
                'roles' => [
                    'label' => 'liip.user::lang.nav.roles',
                    'icon' => 'icon-address-card',
                    'order' => 100,
                    'permissions' => ['liip.user.roles'],
                    'url' => Backend::url('liip/user/userroles'),
                ]
            ]);
        });
    }

    /**
     * Extend Rainlab.User form fields.
     *
     * @return void
     */
    protected function extendRainlabUserFormFields()
    {
        Event::listen('backend.form.extendFields', function ($widget) {
            $controller = $widget->getController();

            // Only for the User model
            if (!$widget->model instanceof User) {
                return;
            }

            $widget->addFields([
                'role' => [
                    'label' => 'liip.user::lang.user.role',
                    'span' => 'full',
                    'type' => 'relation',
                    'emptyOption' => '--',
                ],
            ]);
            $widget->addTabFields([
                'api_token' => [
                    'label' => 'liip.user::lang.user.api_token',
                    'span' => 'full',
                    'type' => 'accesstoken',
                    'tab' => 'rainlab.user::lang.user.account'
                ],
            ]);
        });
        Event::listen('backend.list.extendColumns', function ($widget) {
            $controller = $widget->getController();
            if (!($controller instanceof Users)) {
                return;
            }
            $widget->addColumns([
                'role' => [
                    'label' => 'liip.user::lang.user.role',
                    'type' => 'text',
                    'select' => 'name',
                    'relation' => 'role',
                ]
            ]);
        });
    }

    /**
     * Extend Rainlab.User user model.
     *
     * @return void
     */
    protected function extendRainlabUserUserModel()
    {
        User::extend(function ($model) {
            $model->belongsTo['role'] = UserRole::class;
            $model->setAppends(['user_permissions']);
            $model->addCasts(['settings' => 'array']);
            $model->addDynamicMethod('getUserPermissionsAttribute', function () use ($model) {
                if (!$model->role) {
                    return [];
                }
                return is_array($model->role->permissions) ? $model->role->permissions : [];
            });
            $model->addDynamicMethod('hasUserPermission', function ($permission) use ($model) {
                return in_array($permission, $model->userPermissions);
            });
        });
    }

    public function registerFormWidgets()
    {
        return [
            'Liip\User\FormWidgets\AccessToken' => 'accesstoken',
        ];
    }
}
