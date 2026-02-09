<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for craete service';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $folder = $this->ask('Folder Name');
        $service = $this->ask('Service Name');
        $namespace = "App\Services\\".$folder;
        $className = $service.'Service';
        $pathRepository = "App\Repositories\\".$folder."\\".$service."Repository";
        $nameRepository = $service."Repository";
        $camelCaseNameRepository = lcfirst($nameRepository);
        $pathModel = "App\Models\\".$folder."\\".$service;
        $nameModel = $service;

        if(!file_exists(app_path('Services/'.$folder))) {
            mkdir(app_path('Services/'.$folder));
        }
        // check if repository already exists
        if (file_exists(app_path('Services/' . $folder . '/' . $service . 'Service.php'))) {
            $this->error('Service already exists!');
            return 0;
        }

        $filecontent = file_get_contents(app_path('Console/Templates/ServiceTemplate.php'));
        $filecontent = str_replace('namespaceName', $namespace, $filecontent);
        $filecontent = str_replace('className', $className, $filecontent);
        $filecontent = str_replace('pathRepository', $pathRepository, $filecontent);
        $filecontent = str_replace('nameRepository', $nameRepository, $filecontent);
        $filecontent = str_replace('camelCaseNameRepository', $camelCaseNameRepository, $filecontent);
        $filecontent = str_replace('pathModel', $pathModel, $filecontent);
        $filecontent = str_replace('nameModel', $nameModel, $filecontent);

        file_put_contents(app_path('Services/' .$folder.'/'. $service . 'Service.php'), $filecontent);
        $this->info('Repository created successfully!');
        return 0;
    }
}
