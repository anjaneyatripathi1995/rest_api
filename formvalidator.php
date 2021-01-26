<?php
/*
*@use Validate Form
*
*/
class FormValidator
{
    public static $regexes = Array(
            'date' => "^[0-9]{4}[-/][0-9]{1,2}[-/][0-9]{1,2}\$",
            'amount' => "^[-]?[0-9]+\$",
            'number' => "^[-]?[0-9,]+\$",
            'alfanum' => "^[0-9a-zA-Z ,.-_\\s\?\!]+\$",
            'not_empty' => "[a-z0-9A-Z]+",
            'words' => "^[A-Za-z]+[A-Za-z \\s]*\$",
            'phone' => "^[0-9]{10,11}\$",
            'zipcode' => "^[1-9][0-9]{3}[a-zA-Z]{2}\$",
            'plate' => "^([0-9a-zA-Z]{2}[-]){2}[0-9a-zA-Z]{2}\$",
            'price' => "^[0-9.,]*(([.,][-])|([.,][0-9]{2}))?\$",
            'anything' => "^[\d\D]{1,}\$",
            'username' => "^[\w]{3,32}\$"
);

private $validations, $mandatories, $equal, $errors, $corrects, $fields;


public function __construct($validations=array(), $mandatories = array(), $equal=array())
{
    $this->validations = $validations;
    $this->mandatories = $mandatories;
    $this->equal = $equal;
    $this->errors = array();
    $this->corrects = array();
}


public function validate($items)
{
    $this->fields = $items;
    $havefailures = false;

    //Check for mandatories
    foreach($this->mandatories as $key=>$val)
    {
        if(!array_key_exists($val,$items))
        {
            $havefailures = true;
            $this->addError($val);
        }
    }

    //Check for equal fields
    foreach($this->equal as $key=>$val)
    {
        //check that the equals field exists
        if(!array_key_exists($key,$items))
        {
            $havefailures = true;
            $this->addError($val);
        }

        //check that the field it's supposed to equal exists
        if(!array_key_exists($val,$items))
        {
            $havefailures = true;
            $this->addError($val);
        }

        //Check that the two fields are equal
        if($items[$key] != $items[$val])
        {
            $havefailures = true;
            $this->addError($key);
        }
    }

    foreach($this->validations as $key=>$val)
    {
            //An empty value or one that is not in the list of validations or one that is not in our list of mandatories
            if(!array_key_exists($key,$items)) 
            {
                    $this->addError($key, $val);
                    continue;
            }

            $result = self::validateItem($items[$key], $val);

            if($result === false) {
                    $havefailures = true;
                    $this->addError($key, $val);
            }
            else
            {
                    $this->corrects[] = $key;
            }
    }

    return(!$havefailures);
}


public function validationStatus() {

    $errors = array();

    $correct = array();

    if(!empty($this->errors))
    {            
        foreach($this->errors as $key=>$val) { $errors[$key] = $val; }            
    }

    if(!empty($this->corrects))
    {
        foreach($this->corrects as $key=>$val) { $correct[$key] = $val; }                
    }

    $output = array('errors' => $errors, 'correct' => $correct);

    //return json_encode($output);
    return $output;
}


/**
 *
 * Adds an error to the errors array.
 */ 
private function addError($field, $type='string')
{
    $this->errors[$field] = $type;
}


/** 
 *
 * Validates a single var according to $type.
 *
 */
public static function validateItem($var, $type)
{
    if(array_key_exists($type, self::$regexes))
    {
            $returnval =  filter_var($var, FILTER_VALIDATE_REGEXP, array("options"=> array("regexp"=>'!'.self::$regexes[$type].'!i'))) !== false;
            return($returnval);
    }
    $filter = false;
    switch($type)
    {
            case 'email':
                    $var = substr($var, 0, 254);
                    $filter = FILTER_VALIDATE_EMAIL;        
            break;
            case 'int':
                    $filter = FILTER_VALIDATE_INT;
            break;
            case 'boolean':
                    $filter = FILTER_VALIDATE_BOOLEAN;
            break;
            case 'ip':
                    $filter = FILTER_VALIDATE_IP;
            break;
            case 'url':
                    $filter = FILTER_VALIDATE_URL;
            break;
    }
    return ($filter === false) ? false : filter_var($var, $filter) !== false ? true :     false;
}           
}

?>
