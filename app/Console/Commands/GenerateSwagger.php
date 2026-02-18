<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use OpenApi\Generator;

class GenerateSwagger extends Command
{
    protected $signature = 'swagger:generate';
    protected $description = 'Generate OpenAPI documentation';

    public function handle()
    {
        // إنشاء كائن Generator
        $generator = new Generator();

        // توليد OpenAPI
        $openapi = $generator->generate([
            app_path(), // مسار المسح
        ]);

        file_put_contents(public_path('openapi.json'), $openapi->toJson());
        $this->info('OpenAPI documentation generated successfully!');
    }
}
