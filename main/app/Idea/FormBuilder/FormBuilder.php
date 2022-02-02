<?php  namespace Idea\FormBuilder;

use Auth;
use \Illuminate\Html\FormBuilder as IlluminateFormBuilder;
use Illuminate\Support\Facades\URL;

/**
 * Rozszerza funkcjonalność \Illuminate\Html\FormBuilder
 * @package Idea\FormBuilder
 */
class FormBuilder extends IlluminateFormBuilder {

    /**
     * @param $desc
     * @param $name
     * @param $route_change
     * @param $route_confirm
     * @param null $object
     * @param string $condition_msg
     * @param string $accept_msg
     * @param string $class
     * @param array $options
     * @param array $col_class
     * @param bool $reConfirm
     * @param bool $conditional_disable
     * @return string
     * @internal param bool $label_accept
     */
    public function confirmation($desc, $name,  $route_change, $route_confirm, $object = null, $condition_msg = '', $accept_msg = '', $class = "", $options = array(), $col_class = array('',''), $reConfirm = false, $conditional_disable = null )
    {
        $options = $this->html->attributes($options);

        if(is_null($conditional_disable))
            $conditional_disable = ( $object->$name == '0000-00-00') ? true : false;

        $confirm_name = $name.'_confirm';
        $formObject = '';
        $formObject .= '<label class="control-label '.$col_class[0].' alert_label" for="'.$name.'">'.$desc.':</label>';

        $formObject .= '<div class="input-group input-group-sm  '.$col_class[1].'  pull-left">';
            $formObject .= '<input name="'.$name.'" id="'.$name.'" type="text" class="form-control datepicker input-sm tips '.$class.'"';

                        if( is_null($object) || $conditional_disable ) {
                            $formObject .= ' disabled="disabled" ';
                            if( $condition_msg != '') $formObject .= ' title="<p>'.$condition_msg.'</p>" ';
                        }else if( !is_null($object->$confirm_name) && $object->$confirm_name != '0000-00-00') {
                            $formObject .= ' disabled = "disabled" ';
                            $formObject .= ' value = "'.$object->$name.'" ';
                        }else {
                            $formObject .= ' value = "'.$object->$name.'" ';
                            $formObject .= ' wreck_id = "'.$object->id.'" ';
                            $formObject .= ' hrf = "'.$route_change.'" ';
                        }
                $formObject .= ' placeholder="'.$desc.'" ';
                $formObject .= ' '.$options.' ';
            $formObject .= '>';
            if(Auth::user()->can('kartoteka_szkody#sprzedaz_wraku#zarzadzaj')) {
                $formObject .= '<span class="input-group-btn">';
                $formObject .= '<div class="btn-group tips" data-toggle="buttons" ';
                if (!is_null($object) && (is_null($object->$confirm_name) || $object->$confirm_name == '0000-00-00')) {
                    $formObject .= ' title = "<p>' . $accept_msg . '</p>" ';
                }
                $formObject .= ' >';
                $formObject .= '<label class="btn btn-confirmation btn-sm ';
                if (!is_null($object) && (!is_null($object->$confirm_name) && $object->$confirm_name != '0000-00-00')) {
                    $formObject .= 'active ';
                }
                $formObject .= ' " ';
                if (
                    (
                        is_null($object) ||
                        ($object->$confirm_name != '0000-00-00' && !is_null($object->$confirm_name)) ||
                        ($conditional_disable || is_null($object)) //&& !Auth::user()->can('kartoteka_szkody#kradziez#edycja')
                    )
                    && $reConfirm == false
                ) {
                    $formObject .= ' disabled = "disabled" ';
                }
                $formObject .= ' ' . $options . ' ';
                $formObject .= ' id="label_check_' . $name . '" >';
                $formObject .= '<input type="checkbox" class="alert_confirmation" ';
                if (!is_null($object)) {
                    $formObject .= ' wreck_id = "' . $object->id . '" ';
                    $formObject .= ' hrf = "' . $route_confirm . '" ';
                }
                $formObject .= ' > <i class="fa fa-check " ></i>';
                $formObject .= '</label>';
                $formObject .= '</div>';
                $formObject .= '</span>';
            }
        $formObject .= '</div>';

        return $formObject;
    }

    /**
     * @param $name
     * @param $desc
     * @param $model
     * @param null $object
     * @param string $inputClass
     * @param array $options
     * @param array $col_class
     * @param string $route
     * @return string
     */
    public function autosaveInput($name, $desc, $model, $object = null, $inputClass = '', $options = array(), $col_class = array('col-sm-12 col-md-6 '), $route = '')
    {
        if(isset($options['disabled']) &&  $options['disabled'] == '')
            unset($options['disabled']);

        $options = $this->html->attributes($options);

        $formObject = '';

        $formObject .='<div class="form-group has-feedback '.$col_class[0].'" id="'.$name.'-group">';
            $formObject .='<label class="control-label" for="'.$name.'">'.$desc.':</label>';
                $formObject .='<input class="form-control input-sm '.$inputClass.'" type="text" name="'.$name.'" id="'.$name.'" ';
                if(!is_null($object)) {
                    $formObject .= ' '.$options.' ';
                    $formObject .= ' value = "'.$object->$name.'" ';
                    if($route == '')
                        $formObject .= ' hrf = "'.URL::route('injuries.info.setValue', array($object->id, $name, $model,$desc)).'" ';
                    else
                        $formObject .= ' hrf = "'.$route.'" ';

                }
                    $formObject .= ' placeholder="'.$desc.'" ';
                $formObject .= ' >';
        $formObject .= '</div>';

        return $formObject;
    }

    public function autosaveSelect($name, $selectOptions, $desc, $model, $object = null, $inputClass = '', $options = array(), $col_class = array('col-sm-6'), $route = '')
    {
        if(isset($options['disabled']) &&  $options['disabled'] == '')
            unset($options['disabled']);

        $options = $this->html->attributes($options);

        $formObject = '';

        $formObject .='<div class="form-group has-feedback '.$col_class[0].'" id="'.$name.'-group">';
        $formObject .='<label class="control-label" for="'.$name.'">'.$desc.':</label>';
        $formObject .='<select class="form-control input-sm '.$inputClass.'" type="text" name="'.$name.'" id="'.$name.'" ';
        if(!is_null($object)) {
            $formObject .= ' '.$options.' ';
            if($route == '')
                $formObject .= ' hrf = "'.URL::route('injuries.info.setValue', array($object->id, $name, $model, $desc)).'" ';
            else
                $formObject .= ' hrf = "'.$route.'" ';

        }
        $formObject .= ' >';
            $formObject .= '<option value="0">--- wybierz ---</option>';
            if(!is_null($object)) {
                foreach( $selectOptions as $k => $selectOption )
                {
                    $formObject .= '<option value="'.$k.'" ';
                    if($object->$name == $k) {
                        $formObject .= 'selected';
                    }
                    $formObject .= ' >'.$selectOption.'</option>';
                }
            }
        $formObject .= '</select>';
        $formObject .= '</div>';

        return $formObject;
    }

    public function reportCheckbox($name, $label = '', $value = 1, $styles = '')
    {
        $formObject = '';
        $formObject .= '<div class="checkbox col-sm-12 col-md-6 col-lg-4" style="'.$styles.'">';
        $formObject .= '<label>';
        $formObject .= '<input name="fields['.$name.']" type="checkbox" value="'.$value.'">'.$label;
        $formObject .= '</label>';
        $formObject .= '</div>';

        return $formObject;
    }
}