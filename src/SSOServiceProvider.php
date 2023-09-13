<?php

namespace Girift\SSO;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SSOServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('sso')
            ->hasConfigFile()
            ->hasMigration('create_sso_tokens_table')
            ->hasRoute('web');
    }
}
