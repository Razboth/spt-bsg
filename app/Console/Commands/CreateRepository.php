<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateRepository extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for craete repository';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $folder = $this->ask('Folder Name');
        $repository = $this->ask('Repository Name');
        $namespace = "App\Repositories\\".$folder;
        $className = $repository.'Repository';

        if(!file_exists(app_path('Repositories/'.$folder))) {
            mkdir(app_path('Repositories/'.$folder));
        }
        // check if repository already exists
        if (file_exists(app_path('Repositories/' . $folder . '/' . $repository . 'Repository.php'))) {
            $this->error('Repository already exists!');
            return 0;
        }

        $filecontent = file_get_contents(app_path('Console/Templates/RepositoryTemplate.php'));
        $filecontent = str_replace('namespaceName', $namespace, $filecontent);
        $filecontent = str_replace('className', $className, $filecontent);

        file_put_contents(app_path('Repositories/' .$folder.'/'. $repository . 'Repository.php'), $filecontent);
        $this->info('Repository created successfully!');
        return 0;
    }
}
