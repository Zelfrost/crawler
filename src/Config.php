<?php

use Symfony\Component\Yaml\Yaml;

class Config
{
    const CONFIG_PATH = __DIR__.'/../parameters.yml';

    private static $config = null;

    public static function get($parameter, $default = null)
    {
        if (null === self::$config) {
            self::$config = Yaml::parse(file_get_contents(self::CONFIG_PATH));
        }

        $parts = explode('.', $parameter);
        $value = self::$config;

        foreach ($parts as $part) {
            if (!isset($value[$part])) {
                return $default;
            }

            $value = $value[$part];
        }

        return $value;
    }

    public static function getLogFile()
    {
        return self::evaluatePath(self::get('log.file'));
    }

    public static function getBaseFile()
    {
        return self::evaluatePath(self::get('base.file'));
    }

    private static function evaluatePath(string $file): string
    {
        if ('/' !== substr($file, 0, 1)) {
            return sprintf(
                '%s/../%s',
                __DIR__,
                $file
            );
        }

        return $file;
    }
}
