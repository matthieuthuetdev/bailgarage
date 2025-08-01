<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit2ffa3415456b12f35e0022d3ddfc3691
{
    public static $files = array (
        '6e3fae29631ef280660b3cdad06f25a8' => __DIR__ . '/..' . '/symfony/deprecation-contracts/function.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Sicaa\\NumberToFrWords\\' => 22,
        ),
        'P' => 
        array (
            'Psr\\Container\\' => 14,
            'PHPMailer\\PHPMailer\\' => 20,
        ),
        'J' => 
        array (
            'JoanFabregat\\SecureTokenGenerator\\' => 34,
        ),
        'F' => 
        array (
            'Faker\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Sicaa\\NumberToFrWords\\' => 
        array (
            0 => __DIR__ . '/..' . '/sicaa/number-to-fr-words/src',
        ),
        'Psr\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/container/src',
        ),
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
        'JoanFabregat\\SecureTokenGenerator\\' => 
        array (
            0 => __DIR__ . '/..' . '/joanfabregat/secure-token-generator/src',
        ),
        'Faker\\' => 
        array (
            0 => __DIR__ . '/..' . '/fakerphp/faker/src/Faker',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit2ffa3415456b12f35e0022d3ddfc3691::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit2ffa3415456b12f35e0022d3ddfc3691::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit2ffa3415456b12f35e0022d3ddfc3691::$classMap;

        }, null, ClassLoader::class);
    }
}
