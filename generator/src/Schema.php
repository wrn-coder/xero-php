<?php
/**
 * @package    xero-php
 * @author     Michael Calcinai <michael@calcin.ai>
 */

namespace Calcinai\XeroPHP\Generator;

use Calcinai\Strut\Definitions\Schema as StrutSchema;

class Schema extends StrutSchema
{
    /**
     * @param StrutSchema $schema
     */
    public function __construct(StrutSchema $schema){

        if($schema->has('allOf')){
            $new_schema = StrutSchema::create();

            foreach($schema->getAllOf() as $subschema){
                foreach($subschema as $property_name => $property){
                    $new_schema->set($property_name, $property);
                }
            }
        } else {
            $new_schema = $schema;
        }

        parent::__construct($new_schema);

    }

    /**
     * @return mixed|null
     */
    public function getType(){

        if($this->has('items')) {
            $target = $this->getItems();
        } else {
            $target = $this;
        }


        if($target->has('type')){
            return parent::get('type');
        } elseif ($target->has('$ref')) {
            sscanf($target->getRef(), '#/definitions/%s', $property_type);
            return $property_type;
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isArray(){
        return $this->has('items');
    }

    /**
     * @return null|string
     */
    public function getDescription(){
        return $this->has('description') ? parent::getDescription() : null;
    }


}