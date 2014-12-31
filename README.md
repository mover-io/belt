# belt
---

A PHP developer toolbelt full of rainbows and awesome output!

Created with love by [@jacobstr](https://github.com/jacobstr) and tried and trusted at [@mover](https://github.com/mover-io).

### Packages:

#### 1) Trace

Provides Error, Logging, and CLI debugging tools through intelligent and colorized stack, source, and result formatted outputs.

Example:

```php
Belt\Trace::debug($your_result);
// with Stack Trace
Belt\Trace::stack(7)->debug($your_result);
```

#### 2) Text

Provides string format helpers for frequent string helpers.

Example:

```php
$route = Belt\Text::ensureNoPrefix($route, "/v2/");
$route = Belt\Text::ensureNoSuffix($route, "/");
```

#### 3) Arrays

Provides array helpers for common actions.

Example:

```php
// Safe getter that returns null if no value for key
$value = Belt\Array::get($array, 'key', null)
```

#### 4) Profile

Provides run time profiling for memory and execution time on function calls.