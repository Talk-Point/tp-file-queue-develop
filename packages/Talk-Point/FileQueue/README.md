# TPTaskRunner

[![Software License][ico-license]](LICENSE.md)
[![Build Status](https://travis-ci.org/Talk-Point/tp-laravel-s3-queue-develop.svg?branch=master)](https://travis-ci.org/Talk-Point/tp-laravel-s3-queue-develop)

This is where your description should go. Try and limit it to a paragraph or two, and maybe throw in a mention of what
PSRs you support to avoid any confusion with users and contributors.

## Install

Via Composer

``` bash
$ composer require Talk-Point/TPTaskRunner
```

Add to the `app/config.php` file in `providers`

```php
'providers' => [
    TPTaskRunner\FileQueueServiceProvider::class,
]
```

## Usage

One Model can have many tasks, so that you create a Model and add the tasks relation.

``` php
/**
 * @return \Illuminate\Database\Eloquent\Relations\MorphMany
 */
public function tasks()
{
    return $this->morphMany('TPTaskRunner\Models\Task', 'taskable');
}
```

To create a new task for your model.

```php
$model = YouModel::create([]);
$task = Task::createTask(OrderSuccess::class);
$model->tasks()->save($task);
$task->run();
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
