# SimpleLogger
Simple PSR-3 compliant logger; less than 200 SLOC and 4 files.

Only one dependency, `psr/log`, use this package with confidence.

This was originally a library that I developed for my own projects, but I thought it might be worth sharing. If you have any comments or spot a bug, feel free to open an issue.  

Tests will eventually come later.

## Features
### Global minimal log level
Define a global minimal log level, useful when switching from development (DEBUG) to development (INFO or even NOTICE).

### Custom writers with individual minimal level
The default writer writes to local log files, but if you want something else, feel free to create a custom writer, implement a single method present in the `SimpleLoggerWriterInterface`, pass it to the logger and you are good to go.
As and added bonus, you can define a higher minimal level per writer. For example: all the logs go to the log file and only the critical ones go through your custom writer.

### Logger proxies
Useful when a prefix needs to be prepended to a log message, for example inside a service class or a controller. Instead of the log message being 
> [INFO] Blah

it becomes 
> [INFO][Context] Blah

This is done through the use of a lightweight wrapper class that references a single global `SimpleLogger` instance, meaning you save on
- time: do not manually prefix your log message for each class
- resources: there is only one "heavy" instance of the logger and its writers, the proxies are, well, proxies

## Installation
```shell
composer require gd-75/simple-logger
```

## Usage
Some every-day use cases, if you think that one use case is missing and should be added, feel free to open an issue :)
### Basic use
```php
<?php
use GD75\SimpleLogger\SimpleLogger;

require_once "vendor/autoload.php";

$logger = new SimpleLogger(SimpleLogger::LEVEL_INFO);

// Minimal log level at logger-level
$logger->debug("I should not be here");
$logger->info("I should be here");

// Non-valid log level (it will always be logged, no matter the minimal log level)
$logger->log("test", "My level was originally a string saying 'test', which is not a known log level");

// Context
$logger->info("I have a context, check it out", ["message" => "Thanks for checking me out!"]);
```

### Default writer (file) with custom paths
```php
<?php
use GD75\SimpleLogger\SimpleLogger;
use GD75\SimpleLogger\FileWriter;

require_once "vendor/autoload.php";

$logger = new SimpleLogger(SimpleLogger::LEVEL_INFO, false);
// Notice that since the minimal level of the logger is INFO, using a lower level here will have no influence
$logger->addWriter(SimpleLogger::LEVEL_DEBUG, new FileWriter("out", "out/contexts"));

$logger->info("It works!");
```


### Proxies
```php
<?php
use GD75\SimpleLogger\SimpleLogger;

require_once "vendor/autoload.php";

$logger = new SimpleLogger(SimpleLogger::LEVEL_INFO);

// Using proxy and proxy of a proxy
$loggerProxy = $logger->getProxy("[MyContext]");
$loggerProxy2 = $loggerProxy->getProxy("[MySubContext]");
$loggerProxy->info("I have gone through a proxy!");
$loggerProxy2->info("This is starting to get out of hands");
```

### Non-default writer
```php
<?php
use GD75\SimpleLogger\SimpleLogger;
use GD75\SimpleLogger\SimpleLoggerWriterInterface;

require_once "vendor/autoload.php";

class CLIWriterSimple implements SimpleLoggerWriterInterface {

    public function write(string $levelName, string $message, array $context): void
    {
        $contextElementCount = count($context);
        echo "[{$levelName}] {$message}. Context has {$contextElementCount} element(s) in it.\n";
    }
}

$logger = new SimpleLogger(SimpleLogger::LEVEL_INFO, false);

// Using a writer with a higher minimal level
$logger->addWriter(SimpleLogger::LEVEL_CRITICAL, new CLIWriterSimple());
$logger->critical("This will be seen in the command line");

// Proxies will of course also work with custom writers
$loggerProxy = $logger->getProxy("[MyContext]");
$loggerProxy->critical("I have gone through a proxy!");
```








