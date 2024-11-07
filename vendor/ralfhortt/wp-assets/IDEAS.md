# Ideas

## Conditional loading

Conditional loading is possible by using the `is` method.
Pass a `callable` or `$hook_suffix` to the function.

```php
<?php
new AdminScript(string $handle, string $source)->is('post.php');
new Style(string $handle, string $source)->is('is_single');
new Login(string $handle, string $source)->is(function(){
    return ( '24.12.2019' == date('d.m.Y') );
});
```
