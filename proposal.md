## Action annotation
`@Action` has only route and methods params. Can't use: `group`, `middleware`, `defaults` and `name`

While adding `defaults` and `middleware` is quite useful,
I don't know where `group` is applicable here, but it should be.

Also, `name` might be very helpful. The main difference between standard `@Route` and keeper `@Controller`+`@Action`
is that `@Controller` allows to set a global actions' prefix and a namespace.

Since we have to use a unique route name
(`my:custom/endpoint-route.name` in standard `@Route` and `controller.action` in `@Action`)
I see no use of having automatic imploding controller action reference into a single string (like `controller.action`),
we can use a standard `@Route` naming (but still apply the prefix from the `@Controller`)

So the proposal is:
- `@Controller` namespace refers to a global prefix (declared somewhere where the namespace is added in the bootloader)
- `@Controller` prefix refers to a local prefix (any empty value is equal to `''`)
- no slashes are auto added or trimmed (except of `/\/+/` to remove `abc///de/`).
This will mean a keeper controller action may look like this: `keeper_users-edit` (this is fully up to a developer)
- no defaults will be automatically suggested by having `prefix=''`
  - a default controller CAN be assigned to a namespace explicitly
  - a default method CAN be declared in the `@Controller` annotation explicitly (by default `index`) 
  - Otherwise, no defaults
- any other standard `@Route` params are applicable. Even using a `group` param will act fully the same,
but on the action level - if a group also has a prefix it will be added right between `@Controller` prefix and `@Action` route
  
  
According to that, the usage will look like the next:
```php
<?php
//...
class KeeperBootloader//...
{
    public function register(string $namespace, string $prefix = null, string $controllerDefault = null): void
    {
        //...
    }

    public function boot(): void
    {
        $this->register('keeper', 'admin/'); //no default controller
        $this->register('profile', 'me/', 'dashboard'); //'dashboard' is the default controller
    }
}
```

```php
/**
@\Spiral\Keeper\Annotation\Controller(namespace="keeper", prefix="people/")
 */
class Users
{
    /**
     * @\Spiral\Keeper\Annotation\Action(name="users:index", route="users") //leading slash presents in the prefix
     */
    public function index(): string
    {
        return 'list';
    }
}
```

````html
Each namespace can have the same route names - they will be isolated
<a href="@action('keeper', 'users:index', [])">Refer to a keeper namespace.</a>
```
