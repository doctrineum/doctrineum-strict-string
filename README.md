# About
[Doctrine](http://www.doctrine-project.org/) [enum](http://en.wikipedia.org/wiki/Enumerated_type) allowing strings only.

### Example
```php
$strictStringEnum = StrictStringEnum::get('foo');
(string)$strictStringEnum === $strictStringEnum->getValue() === 'foo'; // true

// throws an exception - only string is allowed
$strictStringEnum = StrictStringEnum::get(12345);

// throws an exception - again only string is allowed
$strictStringEnum = StrictStringEnum::get(null);
```

# Doctrine integration
For details about new Doctrine type registration, see the parent project [Doctrineum](https://github.com/jaroslavtyc/doctrineum).
