# TP-TaskRunner

[![Software License][ico-license]](LICENSE.md)
[![Build Status](https://travis-ci.org/Talk-Point/tp-task-runner-develop.svg?branch=master)](https://travis-ci.org/Talk-Point/tp-task-runner-develop)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Talk-Point/tp-task-runner/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Talk-Point/tp-task-runner/?branch=master)

Task runner framework for Laravel.

## Install

Via Composer

``` bash
$ composer require Talk-Point/TPTaskRunner
$ php artisan queue:table
$ php artisan queue:failed-table
$ php artisan migrate --seed
```

Add to the `app/config.php` file in `providers`

```php
'providers' => [
    TPTaskRunner\TaskRunnerServiceProvider::class,
]
```

## Usage

To use the tasks polymorph relation extends your model from the `TPTaskRunner\Models\TaskRelationBaseModel` model from the Framework.
Than you can get the tasks with the method `$model->tasks()->get()`

__Task:__

To create a task, with `run()` you start the task.

```php
$model = YouModel::create([]);
$task = \TPTaskRunner\Models\Task::createTask($job_class);
$model->tasks()->save($task);
$task->run();
```

__Task with Data:__

To create a task with data.

```php
# Create
$task = \TPTaskRunner\Models\Task::createTaskWithData('Class', ['key' => 'value']);
$task->save();

# In Job Class
$o = $task_load->getJSONData();
$data = $o->key;
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email it@talk-point.de instead of using the issue tracker.

## Credits

- [Talk-Point-IT][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/Talk-Point/TPTaskRunner.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/Talk-Point/TPTaskRunner/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/Talk-Point/TPTaskRunner.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/Talk-Point/TPTaskRunner.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/Talk-Point/TPTaskRunner.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/Talk-Point/TPTaskRunner
[link-travis]: https://travis-ci.org/Talk-Point/TPTaskRunner
[link-scrutinizer]: https://scrutinizer-ci.com/g/Talk-Point/TPTaskRunner/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/Talk-Point/TPTaskRunner
[link-downloads]: https://packagist.org/packages/Talk-Point/TPTaskRunner
[link-author]: https://github.com/Talk-Point
[link-contributors]: ../../contributors
