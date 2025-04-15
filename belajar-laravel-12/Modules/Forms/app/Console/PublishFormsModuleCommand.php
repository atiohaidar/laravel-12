<?php

namespace Modules\Forms\app\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class PublishFormsModuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forms:publish 
                            {--force : Force overwrite of existing files}
                            {--config : Publish configuration files only}
                            {--views : Publish view files only}
                            {--migrations : Publish migration files only}
                            {--assets : Publish asset files only}
                            {--all : Publish everything}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish the Forms module resources';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Publishing Forms Module resources...');

        $options = ['--provider' => 'Modules\Forms\Providers\FormsServiceProvider'];
        
        if ($this->option('force')) {
            $options['--force'] = true;
        }

        if ($this->option('all')) {
            $this->call('vendor:publish', $options);
        } else {
            if ($this->option('config')) {
                $this->publishConfig();
            }
            
            if ($this->option('views')) {
                $this->publishViews();
            }
            
            if ($this->option('migrations')) {
                $this->publishMigrations();
            }
            
            if ($this->option('assets')) {
                $this->publishAssets();
            }
            
            // If no specific options were chosen, prompt the user
            if (!$this->option('config') && 
                !$this->option('views') && 
                !$this->option('migrations') && 
                !$this->option('assets')) {
                $this->promptUser();
            }
        }

        $this->info('Forms module resources published successfully!');
        
        if ($this->confirm('Would you like to run the Forms module migrations now?', true)) {
            $this->call('module:migrate', ['module' => 'Forms']);
        }

        return Command::SUCCESS;
    }
    
    /**
     * Publish configuration files.
     */
    private function publishConfig(): void
    {
        $this->info('Publishing configuration files...');
        $options = [
            '--tag' => 'forms-config',
        ];
        
        if ($this->option('force')) {
            $options['--force'] = true;
        }
        
        $this->call('vendor:publish', $options);
    }
    
    /**
     * Publish view files.
     */
    private function publishViews(): void
    {
        $this->info('Publishing view files...');
        $options = [
            '--tag' => 'forms-module-views',
        ];
        
        if ($this->option('force')) {
            $options['--force'] = true;
        }
        
        $this->call('vendor:publish', $options);
    }
    
    /**
     * Publish migration files.
     */
    private function publishMigrations(): void
    {
        $this->info('Publishing migration files...');
        $options = [
            '--tag' => 'forms-migrations',
        ];
        
        if ($this->option('force')) {
            $options['--force'] = true;
        }
        
        $this->call('vendor:publish', $options);
    }
    
    /**
     * Publish asset files.
     */
    private function publishAssets(): void
    {
        $this->info('Publishing asset files...');
        $options = [
            '--tag' => 'forms-assets',
        ];
        
        if ($this->option('force')) {
            $options['--force'] = true;
        }
        
        $this->call('vendor:publish', $options);
    }
    
    /**
     * Prompt the user for what to publish.
     */
    private function promptUser(): void
    {
        $choices = $this->choice(
            'What would you like to publish?',
            [
                'all' => 'All resources',
                'config' => 'Configuration',
                'views' => 'Views',
                'migrations' => 'Migrations',
                'assets' => 'Assets',
            ],
            'all',
            null,
            true
        );
        
        $choices = (array) $choices;
        
        foreach ($choices as $choice) {
            switch ($choice) {
                case 'all':
                    $options = ['--provider' => 'Modules\Forms\Providers\FormsServiceProvider'];
                    if ($this->option('force')) {
                        $options['--force'] = true;
                    }
                    $this->call('vendor:publish', $options);
                    return;
                case 'config':
                    $this->publishConfig();
                    break;
                case 'views':
                    $this->publishViews();
                    break;
                case 'migrations':
                    $this->publishMigrations();
                    break;
                case 'assets':
                    $this->publishAssets();
                    break;
            }
        }
    }
}