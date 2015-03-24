[![Build Status](https://travis-ci.org/jaroslavtyc/doctrineum-strict-string.svg)](https://travis-ci.org/jaroslavtyc/doctrineum-strict-string)

# About
[Doctrine](http://www.doctrine-project.org/) [enum](http://en.wikipedia.org/wiki/Enumerated_type) allowing strictly string only.

### Example
```php

$stringEnum = StrictStringEnum::getEnum('foo bar');

$stringEnum = StrictStringEnum::getEnum('12345');

// throws an exception - only string is allowed
$stringEnum = StrictStringEnum::get('');

// throws an exception - only string is allowed
StrictStringEnum::get(12);

// throws an exception - only string is allowed
StrictStringEnum::get(false);

// throws an exception - only string is allowed
StrictStringEnum::get(null);

// throws an exception - only string is allowed
StrictStringEnum::get(new ObjectWithToStringMethod('foo'));

```

# Doctrine integration
For details about new Doctrine type registration, see the parent project [Doctrineum](https://github.com/jaroslavtyc/doctrineum).
