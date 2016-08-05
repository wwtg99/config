# Config loader

## Installation

#### Install from composer
```
composer require "wwtg99/config"
```
Or in composer.json require
```
"wwtg99/config": "*"
```

## Usage

```
$conf = new ConfigPool();  // or new ConfigPool('path/to/cache_file') to enable cache
// Define config resource
// File config source should specify conf directories and conf files. Config will be merged by array_merge().
$source = new FileSource(['default__conf', 'user_conf'], ['conf1.json', 'conf2.php']);
// Define file loader to handle conf files. Supported loaders: JsonLoader, PHPLoader, YamlLoader.
$source->addLoader(new JsonLoader())->addLoader(new PHPLoader());
// Redis source should defined the key (default config) and redis parameters.
// If use redis, should disable cache to get better performance and enable to deploy central configuration.
// $source = new RedisSource();
// Set source for config, one source or an array of sources
$conf->addSource($source);
// Load config
$conf->load();
// Get config, support dot(.) to search in array, ex: a.b.c search for a['b']['c']
$val = $conf->get('name', 'default_val');
// Use .(dot) to search array
$val = $conf->get('arr.val');
// Set config
$conf->set('name', 'val');
// Get changed config by export()
$config = $conf->export();
// Save changed config to cache if use cache file
$conf->saveCache();
```

If use cache file and cache file exists, config will always loaded from cache regardless of origin conf files.
