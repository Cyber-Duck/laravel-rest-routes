Laravel REST Routes
===================

An extension to the Laravel Router to add an extra method ``->rest()`` which
creates RESTful routes similar to Laravel's built in resource.

Installation
------------

Add the package to your `composer.json` and run `composer update`.

    {
        "require": {
            "cyber-duck/restrouter": "dev-master"
        }
    }

Add the service provider in `app/config/app.php`:

    'CyberDuck\RestRouter\RestRouterServiceProvider',

This will add our route class to the IoC container in place of the default,
everything will work as standard with the addition of ``Route::rest()``

Example Usage
-------------

    [app/routes.php]

    <?php
    $options = ['model' => 'ResourceModel'];
    Route::rest('resource-name', 'ResourceController', ['model' => 'ResourceModel']);

This is will register the following routes:

| URI                                                     | Name             | Action                           |
|---------------------------------------------------------|------------------|----------------------------------|
| GET|HEAD resource-name                                  | resource.index   | ResourceController@index         |
| POST resource-name                                      | resource.store   | ResourceController@store         |
| GET|HEAD resource-name/{ResourceModel}/{_path?}         | resource.show    | ResourceController@show          |
| PUT resource-name/{ResourceModel}/{_path?}              | resource.replace | ResourceController@replace       |
| PATCH resource-name/{ResourceModel}/{_path?}            | resource.update  | ResourceController@update        |
| DELETE resource-name/{ResourceModel}/{_path?}           | resource.destroy | ResourceController@destroy       |
| GET|HEAD|POST|PUT|PATCH|DELETE resource-name/{_missing} |                  | ResourceController@missingMethod |

``{_path}`` will capture the remainder of the path after the matching the first
part.  The controller is also RESTful if you need to add any additional routes.

Options include model, except and only and work the same as ``resource()``
