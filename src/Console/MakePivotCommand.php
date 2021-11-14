<?php

namespace TimWassenburg\PivotTableGenerator\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class MakePivotCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:pivot {first_table_name} {second_table_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new pivot table';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Migration';

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     *
     * @throws FileNotFoundException
     */
    protected function buildClass($name): string
    {
        $stub = $this->files->get($this->getStub());

        return $this->populateStub($stub);
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param string $stub
     * @return string
     */
    protected function populateStub(string $stub): string
    {
        list($firstTable, $secondTable) = $this->getSortedInput();

        $searches = [
            [
                '{{ first_table_name }}',
                '{{ second_table_name }}',
                '{{ class }}',
                '{{ pivot_table }}'
            ],
        ];

        foreach ($searches as $search) {
            $stub = str_replace(
                $search,
                [
                    $firstTable,
                    $secondTable,
                    $this->getClassName(),
                    $this->getNameInput()
                ],
                $stub
            );
        }

        return $stub;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        $name = Str::studly($this->getNameInput());

        return "Create{$name}Table";
    }

    /**
     * @return array
     */
    protected function getSortedInput(): array
    {
        $inputValues = [
            $this->getSingularInput('first_table_name'),
            $this->getSingularInput('second_table_name')
        ];
        sort($inputValues);

        return $inputValues;
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput(): string
    {
        $sortedInput = $this->getSortedInput();

        return implode('_', $sortedInput);
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getSingularInput(string $name): string
    {
        return Str::lower(Str::singular(trim($this->argument($name))));
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__ . '/../../stubs/pivot_migration.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '/../database/migrations';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['first_table_name', InputArgument::REQUIRED, 'The name of the first table'],
            ['second_table_name', InputArgument::REQUIRED, 'The name of the second table'],
        ];
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name): string
    {
        $filename = now()->format('Y_m_d_his') . "_create_" . $this->getNameInput() . "_table.php";

        return database_path('migrations/' . $filename);
    }
}
