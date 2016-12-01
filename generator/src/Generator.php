<?php

/**
 * @package    xero-php
 * @author     Michael Calcinai <michael@calcin.ai>
 */
namespace Calcinai\XeroPHP\Generator;

use Calcinai\Strut\Swagger;
use Calcinai\XeroPHP\Generator\Schema\Definition;
use Calcinai\XeroPHP\Generator\Schema\Property;
use Calcinai\XeroPHP\Model;
use ICanBoogie\Inflector;
use PhpParser\BuilderFactory;

class Generator
{

    private $output_directory;
    private $model_namespace;

    public function __construct($output_directory, $model_namespace)
    {
        $this->output_directory = $output_directory;
        $this->model_namespace = $model_namespace;

        $this->inflector = Inflector::get();

        $this->builder_factory = new BuilderFactory();
        $this->printer = new Printer(['shortArraySyntax' => true]);

    }


    public function generate($namespace, Swagger $schema)
    {
        $counter = 0;

        foreach ($schema->getDefinitions() as $model_name => $definition) {
            $definition = new Definition($definition);

            if ($counter++ < 3) continue;

            $relative_model_name = sprintf('%s\\%s', $namespace, $model_name);

            $model = $this->buildModel($relative_model_name, $definition);

            echo $this->printer->prettyPrintFile([$model]);
            break;

        }

        foreach ($schema->getPaths() as $path => $path_item) {
//            $path_item = new Schema();
//            if ($path_item->ha)

        }


    }

    /**
     * Make the AST for a model/definition
     * @param $model_name
     * @param Schema|Definition $definition
     * @return \PhpParser\Node
     */
    private function buildModel($model_name, Definition $definition)
    {

        $class_resolver = new ClassResolver($this->model_namespace);
        $fq_base_class = $class_resolver->addClass(Model::class);
        $fq_model_class = $class_resolver->addClass($model_name);

        $namespace = $this->builder_factory->namespace($class_resolver->getNamespace($fq_model_class));

        $class = $this->builder_factory
            ->class($class_resolver->getClassName($fq_model_class))
            ->extend($class_resolver->getClassName($fq_base_class));


        $class_doc_lines = [];

        foreach ($definition->getProperties() as $property_name => $property) {
            $property = new Property($property);

            $type = $property->getType();

            if ($property->isArray()) {
                $type .= '[]';
            }

            $class_doc_lines[] = sprintf('@property %s %s - %s', $type, $property_name, $property->getDescription());


        }


        $class->setDocComment(self::formatDocComment($class_doc_lines));

        if ($definition->has('required')) {
            $class->addStmt($this->builder_factory->property('required')
                ->makeProtected()
                ->makeStatic()
                ->setDefault($definition->getRequired())
                ->setDocComment($this->formatDocComment(['Mandatory properties for the object', '@var array'])));
        }


        //Append all the 'used' classes
        foreach ($class_resolver->getForeignClasses($fq_model_class) as $fq_class) {
            $use = $this->builder_factory->use($fq_class);

            if (null !== $alias = $class_resolver->getClassAlias($fq_class)) {
                $use->as($alias);
            }

            $namespace->addStmt($use);
        }


        return $namespace->addStmt($class)->getNode();
    }


    /**
     * Format an array into a doccomment
     *
     * @param $lines
     * @return string
     */
    public static function formatDocComment($lines)
    {
        return sprintf("/**\n * %s\n */", implode("\n * ", $lines));
    }

}