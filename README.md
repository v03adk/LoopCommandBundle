# LoopCommandBundle

## Installation and configuration:

Pretty simple with [Composer](http://packagist.org), run:

```sh
composer require kzdali/loopcommandbundle
```

### Add LoopCommandBundle to your application kernel

```php
// app/AppKernel.php
public function registerBundles()
{
    return array(
        // ...
        new KzDali\LoopCommandBundle\LoopCommandBundle(),
        // ...
    );
}
```

### Configuration example

You should configure commands to loop in config.yml

```yaml
loop_commands:
    commands: [cache:clear, doctrine:migrations:generate]                      # comma separated commands
```

To kill loop process

```sh
php app/console loop_command:kill_process
```