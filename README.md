# Config loader

## Installation

#### Install from composer
```
composer require "wwtg99/config"
```

## Usage

```
$conf = new ConfigPool();  // or new ConfigPool('path/to/cache_file') to enable cache
// define config resource
// file config source should specify conf directories and conf files. Config will be merged by array_merge().
$source = new FileSource(['default__conf', 'user_conf'], ['conf1.json', 'conf2.php']);
// define file loader to handle conf files
$source->addLoader(new JsonLoader())->addLoader(new PHPLoader());
// set source for config, one source or an array of sources
$conf->addSource($source);
// load config
$conf->load();
// Get config
$val = $conf->get('name', 'default_val');
// Use .(dot) to search array
$val = $conf->get('arr.val');
// Set config
$conf->set('name', 'val');
// Get changed config by export()
$config = $conf->export();
// Sace changed config to cache if use cache file
$conf->saveCache();
```

If use cache file and cache file exists, config will always loaded from cache regardless of origin conf files.
