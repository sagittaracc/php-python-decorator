# PHP Python Decorator
Python style decorators for PHP

# Requirements
PHP 8.1 or higher

# Install
`composer require sagittaracc/php-python-decorator`

# Example
How long it takes to run a method. See the [`Timer`](https://github.com/sagittaracc/php-python-decorator/blob/main/tests/decorators/Timer.php) decorator
```php

use Sagittaracc\PhpPythonDecorator\Decorator;

class Calc
{
    use Decorator;

    #[Timer]
    function sum($a, $b)
    {
        sleep(1);
        return $a + $b;
    }
}
```
This is how you can call it
```php
$calc = new Calc();
echo call_decorator_func_array([$calc, 'sum'], [1, 2]); // Total execution: 1.00034234 ms; Result: 3
```
Or inline
```php
$timerOnSum = (new Timer)->wrapper(fn($a, $b) => $calc->sum($a, $b));
echo $timerOnSum(1, 2); // Total execution: 1.00034234 ms; Result: 3
```

# Generics
```php
use Sagittaracc\PhpPythonDecorator\Decorator;
use Sagittaracc\PhpPythonDecorator\modules\generics\aliases\T;
use Sagittaracc\PhpPythonDecorator\modules\validation\core\validators\ArrayOf;

#[T]
class Box
{
    use Decorator;

    #[ArrayOf(T::class)]
    public $items;

    public function addItem(#[T] $item)
    {
        $this->items[] = $item;
    }
}

$box = new Box();
$box(Pen::class); // new Box<Pen>();
call_decorator_func_array([$box, 'addItem'], [new Pencil]); // throws a GenericError
```

# Validation
```php

use Sagittaracc\PhpPythonDecorator\Decorator;
use Sagittaracc\PhpPythonDecorator\tests\examples\Progress;
use Sagittaracc\PhpPythonDecorator\tests\validators\Length;
use Sagittaracc\PhpPythonDecorator\tests\validators\SerializeOf;
use Sagittaracc\PhpPythonDecorator\tests\validators\In;
use Sagittaracc\PhpPythonDecorator\tests\validators\LessThan;
use Sagittaracc\PhpPythonDecorator\tests\validators\UInt8;

class Progress
{
    use Decorator;

    #[UInt8]
    public $max;

    #[UInt8]
    #[LessThan('max')]
    public $pos;

    #[In('progress', 'finish', 'aborted')]
    public $status;

    #[Length(32)]
    public string $caption;
}

$progress = new Progress();

set_decorator_prop($progress, 'max', 255);  // max uint8 - 255
set_decorator_prop($progress, 'pos', 100);  // should be less than max
set_decorator_prop($progress, 'status', 'progress');  // status is one of possible cases (progress, finish or aborted)
set_decorator_prop($progress, 'caption', 'in progress ...');  // just a string (max length is 32)
```

# Rpc
```php
use Sagittaracc\PhpPythonDecorator\Decorator;
use Sagittaracc\PhpPythonDecorator\modules\rpc\core\Rpc;

#[Rpc]
class Controller
{
    use Decorator;

    public function sum($a, $b)
    {
        return $a + $b;
    }
}
```

in `index.php`

```php
$requestBody = file_get_contents('php://input');

$controller = new Controller();
$controller($requestBody);
```

in `terminal`

```console
$ curl -d "{"id":1,"method":"sum","params":[1,4]}" http://localhost:4000
{"json-rpc":"2.0","id":1,"result":5}
```

# Console
```php
use Sagittaracc\PhpPythonDecorator\Decorator;
use Sagittaracc\PhpPythonDecorator\modules\console\core\Console;

class Controller
{
    use Decorator;

    #[Console('hello')]
    function greetingPerson($name)
    {
        return "Hello, $name";
    }
}
```

in the command line it would be calling for example something like this:

`php index.php -c hello --name Yuriy`

then in `index.php` you should read the command and the parameters and after that call it like this:

```php
(new Console('hello'))->setParameters(['name' => 'Yuriy'])->getMethod(Controller::class)->run();
```
