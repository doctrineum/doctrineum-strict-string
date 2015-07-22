[![Build Status](https://travis-ci.org/jaroslavtyc/doctrineum-strict-string.svg)](https://travis-ci.org/jaroslavtyc/doctrineum-strict-string)

# About
[Doctrine](http://www.doctrine-project.org/) [enum](http://en.wikipedia.org/wiki/Enumerated_type) allowing strings only.

### Example
```php

$stringEnum = StrictStringEnum::getEnum('foo bar');

$stringEnum = StrictStringEnum::getEnum('12345');

// throws an exception - only string is allowed
$stringEnum = StrictStringEnum::getEnum('');

// throws an exception - only string is allowed
StrictStringEnum::getEnum(12);

// throws an exception - only string is allowed
StrictStringEnum::getEnum(false);

// throws an exception - only string is allowed
StrictStringEnum::getEnum(null);

// throws an exception - only string is allowed
StrictStringEnum::getEnum(new ObjectWithToStringMethod('foo'));

```

# Doctrine integration
For details about new Doctrine type registration, see the parent project [Doctrineum](https://github.com/jaroslavtyc/doctrineum).
