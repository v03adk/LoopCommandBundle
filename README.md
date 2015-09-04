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

### Configuration example and Usage

You should configure commands to loop in config.yml

```yaml
loop_commands:
    commands: [cache:clear, doctrine:migrations:generate]                      # comma separated commands
```

Loop process starts automatically by CheckLoopRunning event listener. When contoller is called, if it implements
LoopCommandInterface loop process starts.

```php
// src/AppBundle/Controller/SomeConteroller.php

use KzDali\LoopCommandBundle\Controller\LoopCommandInterface;

// ...

class SomeController extends Controller implements LoopCommandInterface
{
    // ...
}
```


To kill loop process

```sh
php app/console loop_command:kill_process
```