<?php
/**
 * CMS - CMS based on laravel
 *
 * @category  CMS
 * @package   Laravel
 */

namespace Cms\Generator\Makes;

use Illuminate\Filesystem\Filesystem;
use Cms\Generator\Commands\ScaffoldMakeCommand;
use Cms\Generator\Localizations\SchemaParser as LocalizationsParser;
use Cms\Generator\Localizations\SyntaxBuilder as LocalizationsBuilder;

class MakeLocalization
{
    use MakerTrait;

    private $language_code;

    /**
     * Create a new instance.
     *
     * @param ScaffoldMakeCommand $scaffoldCommand
     * @param Filesystem $files
     * @return void
     */
    public function __construct(ScaffoldMakeCommand $scaffoldCommand, Filesystem $files)
    {
        $this->files = $files;
        $this->scaffoldCommandObj = $scaffoldCommand;
        $this->language_code = $this->scaffoldCommandObj->option('lang');

        $this->start();
    }

    /**
     * Start make seed.
     *
     * @return void
     */
    protected function start()
    {
        $path = $this->getPath($this->language_code . '/'.$this->scaffoldCommandObj->getObjName('Name'), 'localization');

        $this->makeDirectory($path);

        if ($this->files->exists($path))
        {
            return $this->scaffoldCommandObj->comment('x ' . $path);
        }

        $this->files->put($path, $this->compileLocalizationStub());
        $this->scaffoldCommandObj->info('+ ' . $path);
    }

    /**
     * Compile the migration stub.
     *
     * @return string
     */
    protected function compileLocalizationStub()
    {
        $stub = $this->files->get(substr(__DIR__,0, -5) . 'Stubs/localization.stub');

        $this->build($stub);

        return $stub;
    }

    private function build(&$stub){
        $this->replaceLocalization($stub);
    }

    private function replaceLocalization(&$stub){
        if($schema = $this->scaffoldCommandObj->option('localization')){
            $schema = (new LocalizationsParser())->parse($schema);
        }

        $schema = (new LocalizationsBuilder())->create($schema, $this->scaffoldCommandObj->getMeta(), 'validation');
        $stub = str_replace('{{localization}}', $schema, $stub);

        return $this;
    }

}
