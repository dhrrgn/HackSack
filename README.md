# HackSack

A Dependency Injection Container for Hack.

**Note: This is still a Work In Progress**

## Requirements

HHVM 3.0.0

## Example

``` php
<?hh

$container = new Container;

$container->bind('foo', 'stdClass');

// $obj is an instance of stdClass
$obj = $container->resolve('foo');
```

