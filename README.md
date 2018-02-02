## php调用高德地图web接口sdk

### 安装方法

```
  composer require luffyzhao/php-amap-web
```

### 使用方法

#### 地理编码API

```php
  include __DIR__ . '/../vendor/autoload.php';

  use \luffyzhao\amapWeb\Georegeo;

  $data = [['name' => '深圳市南山区科苑北路科兴科学园A4栋501室'], ['name' => '深圳市光明新区高新路研祥智谷研发楼']];

  $geore = new Georegeo($data, ['addressKey' => 'name']);

  $geore->setAmapKey('your key')->solve();

  print_r($geore->toArray());
```
