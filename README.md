# About
[Doctrine](http://www.doctrine-project.org/) [enum](http://en.wikipedia.org/wiki/Enumerated_type) allowing strictly string only.

### Example
```php

$stringEnum = StrictString::getEnum('foo bar');

$stringEnum = StrictString::getEnum('12345');

// throws an exception - only string is allowed
$stringEnum = integerEnum::get('');

// throws an exception - only string is allowed
integerEnum::get(12);

// throws an exception - only string is allowed
integerEnum::get(false);

// throws an exception - only string is allowed
integerEnum::get(null);

// throws an exception - only string is allowed
integerEnum::get(new ObjectWithToStringMethod('foo'));

```

# Doctrine integration
For details about new Doctrine type registration, see the parent project [Doctrineum](https://github.com/jaroslavtyc/doctrineum).
