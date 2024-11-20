<?php

namespace Yaza\LaravelGoogleDriveStorage;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Yaza\LaravelGoogleDriveStorage\Commands\LaravelGoogleDriveStorageCommand;

class LaravelGoogleDriveStorageServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-google-drive-storage')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-google-drive-storage_table')
            ->hasCommand(LaravelGoogleDriveStorageCommand::class);
    }

    public function bootingPackage()
    {
        try { 
            Storage::extend('google', function ($app, $config) {
                // @ Log when extending the driver
                Log::info('Extending Google Drive Storage Driver');
            
                $options = [];
            
                if (!empty($config['teamDriveId'] ?? null)) {
                    $options['teamDriveId'] = $config['teamDriveId'];
                }
            
                // @ Create Google client
                $client = new \Google\Client();
                $client->setClientId($config['clientId']);
                $client->setClientSecret($config['clientSecret']);
            
                // @ Log client configuration
                Log::info('Google Client configured with Client ID: ' . $config['clientId']);

                $config['access_token'] = $config['access_token'] ?? "DEF_TOKEN";
            
                $client->setAccessToken($config['access_token']);
            
                // @ Check if access token has expired
                if ($client->isAccessTokenExpired()) {
                    Log::warning('Access Token expired, refreshing with refresh token');
            
                    $newToken = $client->fetchAccessTokenWithRefreshToken($config['refreshToken']);
            
                    // @ Log successful access token refresh
                    Log::info('Access Token refreshed successfully');
            
                    // @ Update access_token in config
                    $config['access_token'] = $newToken['access_token'];
            
                    // @ Save new access token to file
                    try {
                        file_put_contents(storage_path('app/google_access_token.json'), json_encode($newToken));
                        Log::info('New Access Token saved to file.');
                    } catch (\Exception $e) {
                        Log::error('Error saving Access Token to file: ' . $e->getMessage());
                    }
                } else {
                    Log::info('Access Token is valid');
                }
            
                // @ Create Google Drive service
                try {
                    $service = new \Google\Service\Drive($client);
                    Log::info('Google Drive Service created successfully.');
                } catch (\Exception $e) {
                    Log::error('Error creating Google Drive Service: ' . $e->getMessage());
                    throw $e; // @ Throw exception if service creation fails
                }
            
                // @ Set up the adapter and driver
                try {
                    $adapter = new \Masbug\Flysystem\GoogleDriveAdapter($service, $config['folder'] ?? '/', $options);
                    $driver = new \League\Flysystem\Filesystem($adapter);
                    Log::info('Google Drive Adapter and Filesystem created successfully.');
                } catch (\Exception $e) {
                    Log::error('Error creating Google Drive Adapter: ' . $e->getMessage());
                    throw $e; // @ Throw exception if adapter creation fails
                }
            
                return new \Illuminate\Filesystem\FilesystemAdapter($driver, $adapter);
            });            
        } catch (\Exception $e) {
            Log::error($e);
        }
    }
}
