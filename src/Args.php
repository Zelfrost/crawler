<?php

/**
 * @method static getParameters(): array
 * @method static getMinPrice()
 * @method static getMaxPrice()
 * @method static authorizeNoPrice(): bool
 * @method static isVerbose(); bool
 * @method static getMode(): string
 */
class Args
{
    private static $processed = false;

    private static $parameters = [];
    private static $minPrice = null;
    private static $maxPrice = null;
    private static $noPrice = true;
    private static $verbose = false;
    private static $mode = 'sms';

    public static function __callStatic($name, $arguments)
    {
        self::loadConfig();

        if ('get' === substr($name, 0, 3)) {
            $name = lcfirst(substr($name, 3));
        } elseif ('is' === substr($name, 0, 2)) {
            $name = lcfirst(substr($name, 2));
        } elseif ('authorize' === substr($name, 0, 9)) {
            $name = lcfirst(substr($name, 9));
        }

        return self::$$name;
    }

    public static function loadConfig()
    {
        if (self::$processed) {
            return;
        }

        global $argv;

        array_shift($argv);
        for ($i = 0; $i < sizeof($argv); $i ++) {
            switch ($argv[$i]) {
                case '-m':
                case '--mode':
                    $mode = $argv[++ $i];
                    if (!in_array($mode, ['sms', 'mail', 'none'])) {
                        break;
                    }

                    self::$mode = $mode;

                    break;
                case '-v':
                case '--verbose':
                    self::$verbose = true;

                    break;
                case '-r':
                case '--range':
                    $range = explode('-', $argv[++ $i]);

                    self::$minPrice = (int) $range[0];
                    self::$maxPrice = (int) $range[1];

                    break;
                case '-min':
                case '--min-price':
                    self::$minPrice = (int) $argv[++ $i];

                    break;
                case '-max':
                case '--max-price':
                    self::$maxPrice = (int) $argv[++ $i];

                    break;
                case '-i':
                case '--ignore-no-price':
                    self::$noPrice = false;

                    break;
                default:
                    $parsed = explode(':', $argv[$i]);

                    self::$parameters[$parsed[0]] = $parsed[1];
            }
        }

        self::$processed = true;
    }
}
