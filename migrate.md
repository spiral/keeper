## Summary
The whole changelog is described below, here you can see the main steps that could be required for a successful migration.
- `breadcrumps.dark.php` renamed into `breadcrumbs.dark.php`. So if you extend it or `common.dark.php` please be aware.
- `@Controller` annotation now don't use `name` attribute for prefixing, use `prefix` attribute explicitly.
- KeeperCore now doesn't protect actions by `namespace.controller.method`, use `@GuardNamespace` and `Guarded` annotations.
- For sitemaps permissions are taken from `@GuardNamespace` and `@Guarded` annotation.
  Note that keeper namespace isn't used here automatically because these annotations come from external module,
  so you need to specify the namespace in the `@GuardNamespace` explicitly. 
  As a fallback to `@GuardNamespace` controller's `namespace.name` is used, method's `name` is a fallback to a missing `@Guarded` annotation.
- `@action` directive is deprecated, use `@keeper` one. It has a new 1st parameter - namespace.
- `RouteRegistry::uri()` helper is deprecated, use `RouteBuilder::uri()` one. It has a new 1st parameter - namespace.
- For better and understandable sitemap sorting use `position` attribute in annotations or `position` option in `Sitemap` element declaring (via code).

## Files
View template `views/layout/breadcrumps.dark.php` renamed into `views/layout/breadcrumbs.dark.php` (typo fixed).
Referenced in `views/layout/common.dark.php` and `views/bundle.dark.php`.

## Guard
Previously, `KeeperCore` protected actions using `namespace.controller.method` permission, now it will be used as a fallback
only if a method doesn't have `@Guarded` annotation. Please add `GuardNamespace(namespace="...")` if missing. 

## Routes

### Annotations
- `@Action` annotation now is fully compatible with a global `@Route` annotation.
`name` property is optional, by default `controller.action` name will be assigned.
> For now router ignores provided group.
- `@Controller` annotation now will not fall back to controller's name if prefix is empty, please define `prefix` attribute explicitly if needed.
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

### Sorting
Introduced `position` annotation property for all kind of sitemap annotations.
```php
/**
 * @\Spiral\Keeper\Annotation\Sitemap\Link(title="System", position=1.0)
 */
```
> Note that annotations wait for float position values

You can use a `position` option in a direct sitemap declaration syntax:
```php
/** @var \Spiral\Keeper\Module\Sitemap $sitemap */
$sitemap->group('dashboard', 'Dashboard', ['icon' => 'home', 'position' => -1.2]);
```

Sitemap module will now provide a sorted list of nested nodes.
It uses either defined `position` value, or an auto-incremented default one.
> Using ascending order.

Note that existed position will cause skipping default's auto-increment for that element.
For example having such 3 items with only one with defined position:
```php
use Spiral\Keeper\Module\Sitemap;

class SitemapBootloader extends \Spiral\Keeper\Bootloader\SitemapBootloader
{
    protected function declareSitemap(Sitemap $sitemap): void
    {
        $sitemap->group('positioned', 'Positioned', ['position' => 0.5]);
        $sitemap->group('first', 'First');
        $sitemap->group('second', 'Second');
    }
}
```
will have the next sorting result:
```
first: 0,
positioned: 0.5,
second: 1
```

### Visibility
By default, all nodes are available for the user, `withVisibleNodes()` allows hiding forbidden nodes.
If any node is forbidden, it will be removed from the tree with all its children.
Also, passing a `$targeNode` will mark all active nodes if match found, so it will allow you to use breadcrumbs.

Permissions are taken from `@GuardNamespace`, `@Guarded` and `@Link` annotations: `<guard Namespace (or controller name)>.<link permission (or guarded permission (or method name))>`.
Use `@Link` permission in cases when method is protected by a context-based permission rule -
for rendering links in the sidebar and breadcrumbs the context can't be passed, so you have to use additional permission for navigation (and register it with allow rule).
In other cases you can rely on standard `@Guarded` permission (or method name) flow.
> Note that keeper namespace isn't used here automatically because these annotations come from external module.

Example with `@GuardNamespace` annotation:
```php
/**
 * @Controller(name="with", prefix="/with", namespace="first")
 * @GuardNamespace(namespace="withNamespace")
 */
class WithNamespaceController
{
    /**
     * @Link(title="A")
     * ...
     */
    public function a(): void
    {
        // permission is "withNamespace.a"
    }

    /**
     * @Link(title="B")
     * @Guarded(permission="permission")
     * ...
     */
    public function b(): void
    {
        // permission is "withNamespace.permission"
    }

    /**
     * @Link(title="C", permission="methodC")
     * @Guarded(permission="permission")
     * ...
     */
    public function с(): void
    {
        // permission is "withNamespace.methodC"
    }
}
```
Example without `@GuardNamespace` annotation:
```php
/**
 * @Controller(name="without", prefix="/without", namespace="second")
 */
class WithoutNamespaceController
{
    /**
     * @Link(title="A")
     * ...
     */
    public function a(): void
    {
        // permission is "without.a"
    }

    /**
     * @Link(title="B")
     * @Guarded(permission="permission")
     * ...
     */
    public function b(): void
    {
        // permission is "without.permission"
    }

    /**
     * @Link(title="C", permission="methodC")
     * @Guarded(permission="permission")
     * ...
     */
    public function с(): void
    {
        // permission is "without.methodC"
    }
}
```

### View templates
`sidebar` and `breadcrumbs` view files updated to explicitly use sitemap namespace and node route (using new `\Spiral\Keeper\Helper\RouteBuilder` helper).
```php
/**
 * @var \Spiral\Keeper\Helper\RouteBuilder $_router_ 
 * @var \Spiral\Keeper\Module\Sitemap      $_sitemap_ 
 * @var \Spiral\Keeper\Module\Sitemap\Node $_node_ 
 */
$_router_->uri($_sitemap_->getNamespace(), $_node_->getOption('route') ?? $_node_->getName());
```
If you have extended the basic view templates please update the code.
> for the `keeper` namespace old syntax will still be valid:
```php
/**
 * @var \Spiral\Keeper\Module\RouteRegistry $_router_ 
 * @var \Spiral\Keeper\Module\Sitemap\Node  $_node_ 
 */
$_router_->uri($_node_->getName());
```

## TODO
Tests:
- grid helper
