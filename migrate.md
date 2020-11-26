## Routes

### Annotations
- `@Action` annotation now is fully compatible with a global `@Route` annotation.
`name` property is optional, by default `controller.action` name will be assigned.
> For now router ignores provided group.
- `@Controller` annotation now can have a `defaultAction` property, see below for more details.


As a fallback route annotations with `name` attribute will be duplicated by a legacy name like `controller.method`.
So, for the `keeper` namespace the next code
```php
/** @var \Spiral\Keeper\Module\RouteRegistry $_router_ */
$_router_->uri('users.edit');
``` 
is equivalent to a new one:
```php
/** @var \Spiral\Keeper\Module\RouteRegistry $_router_ */
$_router_->uri('edit:user:route');
```

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

or via `defaultAction` property in `@Controller` annotation.
>`index` method will be used as a fallback.

### URI
`RouteRegistry::uri()` is deprecated, `@action` directive as well. Use `RouteBuilder::uri()` and `@keeper` instead.
> New uri builders now require the namespace explicitly.

**It is highly recommended avoiding using `@action` directive with functions or expressions as 2nd argument**:
```html
<a href="@action('profile', injected('anything') ? inject('something') : [])">[[Profile]]</a>
```

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

### Custom sitemap declaration
In order to sync sitemap annotations and custom sitemap generation you need to extend
`\Spiral\Keeper\Bootloader\SitemapBootloader::declareSitemap` and declare required nodes inside:
```php
<?php

declare(strict_types=1);

use Spiral\Keeper\Module\Sitemap;

class SitemapBootloader extends \Spiral\Keeper\Bootloader\SitemapBootloader
{
    protected function declareSitemap(Sitemap $sitemap): void
    {
        $group = $sitemap->group('group', 'Group Name');
        $group->link('group.index', 'Index page');
    }
}
```

This will allow referring to that links in the annotations via `parent` attribute:
```php
/**
 * @\Spiral\Keeper\Annotation\Sitemap\Link(title="Sub-link", parent="group.index")
 * @\Spiral\Keeper\Annotation\Action(route="/")
 */
```

### View templates
`sidebar` and `breadcrumbs` view files updated to explicitly use sitemap namespace and node route (using new `\Spiral\Keeper\Helper\RouteBuilder` helper).
```php
/**
 * @var \Spiral\Keeper\Helper\RouteBuilder $_router_ 
 * @var \Spiral\Keeper\Module\Sitemap      $_sitemap_ 
 * @var \Spiral\Keeper\Module\Sitemap\Node $_s_ 
 */
$_router_->uri($_sitemap_->getNamespace(), $_s_->getOption('route') ?? $_s_->getName());
```
If you have extended the basic view templates please update the code.
> for the `keeper` namespace old syntax will still be valid:
```php
/**
 * @var \Spiral\Keeper\Module\RouteRegistry $_router_ 
 * @var \Spiral\Keeper\Module\Sitemap       $_sitemap_ 
 * @var \Spiral\Keeper\Module\Sitemap\Node  $_s_ 
 */
$_router_->uri($_s_->getName());
```

## TODO
- sitemap active path
- grid helper
