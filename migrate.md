## Routes

### Annotations
- `@Action` annotation now is fully compatible with a global `@Route` annotation.
> For now router ignores provided group.
- `@Controller` annotation now can have a `defaultAction` property, see below for more details.

### Route defaults
To enable default controller routing it should be added explicitly to the config.
Either via `KeeperBootloader::DEFAULT_CONTROLLER` value or via config file:
```php
return [
     'routeDefaults' => ['controller' => '\App\Controller\DefaultController'],
];
```

Default controller action should be either defined in the config:
```php
return [
     'routeDefaults' => ['controller' => '\App\Controller\DefaultController', 'action' => 'action'],
];
```

or via `defaultAction` property in `@Controller` annotation. `index` method name will be used as a fallback.

### URI
`RouteRegistry::uri()` is deprecated, `@action` directive as well. Use `RouteBuilder::uri()` and `@keeper` instead.
> New uri builders now require the namespace explicitly.

Example:
```php
$rb = $this->container->get(\Spiral\Keeper\Helper\RouteBuilder::class);
$rb->uri('profile', 'subscriptions:list');
```
or
```html
<a href="@keeper('profile', 'subscriptions:list')">[[Subscriptions]]</a>
```

## Sitemaps
`TBD`
