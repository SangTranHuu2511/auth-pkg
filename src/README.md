# Auth SDK

Auth SDK thực hiện extend guard, custom UserProvider, cho phép thực hiện authenticate với một service authentication khác. SDK hỗ trợ việc xác thực chuỗi JSON Web Token nội bộ.

Thư viện chỉ dùng cho Laravel.

### Cách cài đặt thư viện

```sh
composer require sangth/auth-sdk:@dev
```

## Cách dùng

### Cấu hình `config/auth.php`

```php
'guards' => [
    'token' => [
        'driver' => 'token',
        'provider' => 'users',
    ],
],
'providers' => [
    'users' => [
        'driver' => 'prep',
        'model' => env('AUTH_MODEL', App\Models\User::class),
        'host' => env('AUTH_HOST'),
    ],
],

### Thiết lập Model

Set model `User` extends [`Sangth\Auth\Sdk\Models\User`](src/Models/User.php).

### Thiết lập ENV

Set `AUTH_HOST` để xác định API thực hiện authenticate.

Set `AUTH_ENABLE_SELF_VERIFY_TOKEN=true` nếu muốn xác thực chuỗi JWT nội bộ. Mặc định là `false`.