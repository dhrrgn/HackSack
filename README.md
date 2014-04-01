# HackSack

A Dependency Injection Container for Hack.

#### Credit

This library is influenced by the [Orno/Di](https://github.com/orno/di) library, so you may see some similarities.


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

