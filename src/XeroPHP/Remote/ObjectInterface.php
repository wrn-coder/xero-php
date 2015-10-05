<?php


namespace XeroPHP\Remote;


interface ObjectInterface {

    /**
     * Get the GUID Property if it exists
     *
     * @return string|null
     */
    public static function getGUIDProperty();

    /**
     * Get a list of properties
     *
     * @return array
     */
    public static function getProperties();

    /**
     * Get the root node name for sending XML/json
     *
     * @return string
     */
    public static function getRootNodeName();

    /**
     * Get the API to use for the object
     *
     * @return string
     */
    public static function getAPIStem();

    /**
     * Get a list of the supported HTTP Methods
     *
     * @return array
     */
    public static function getSupportedMethods();

    /**
     * return the URI of the resource (if any)
     *
     * @return string
     */
    public static function getResourceURI();

    public static function isPageable();

}