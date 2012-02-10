<?php
/**
 * Formr
 *
 * A Twitter Bootstrap Form Builder
 *
 * This is an object class, meaning you can create multiple Formr objects
 * at the same time.
 *
 * @package    Core
 * @since      1.0
 * @author     srtfisher
**/
class Formr
{
     /**
      * The Input Segments
      *
      * @global     array
     **/
     private $segments = array();
     
     /**
      * The Rendered Content
      *
      * @global     string
      * @access     private
     **/
     private $rendered;
     
     /**
      * Attributes for the form tag
      *
      * @global     array
     **/
     private $attr = array();
     
     /**
      * A Filter to be passed the form data upon rendering
      *
      * @global     object
     **/
     private $filter = NULL;
     
     /**
      * Add an Input Method to the Form
      *
      * @access     public
      * @param      object
     **/
     public function add($obj)
     {
          $this->segments[] = $obj;
     }
     
     /**
      * Set a Form Attribute
      *
      * @param      string
      * @param      string
     **/
     public function attr($key, $val, $override = FALSE)
     {
          if ($override)
               return $this->attr[$key] = array($val);
          else
               return $this->attr[$key][] = $val;
     }
     
     /**
      * Set the Action
      *
      * @access     public
      * @param      string
     **/
     public function action($where)
     {
          if (! valid_url($where))
               $where = site_url($where);
          
          $this->attr('action', $where, TRUE);
     }
     
     /**
      * Render and Compile the Form
      *
      * @access     public
      * @return     void
     **/
     public function render()
     {
          // CodeIgniter
          $CI = get_instance();
          
          // Form Open
          if (! isset($this->attr['method']))
               $this->attr['method'][] = 'post';
          
          if (! isset($this->attr['action']))
               $this->attr['method'][] = current_url();
          
          // Export
          $html = '<form ';
          
          // Form Attributes
          foreach($this->attr as $key => $val)
          {
               $html .= $key.'="'.implode(' ', $val).'" ';
          }
          
          $html .= '>';
          
          // Add CSRF field if enabled, but leave it out for GET requests and requests to external websites	
		if ($CI->config->item('csrf_protection') === TRUE AND ! (strpos(reset($this->attr['action']), $CI->config->site_url()) === FALSE OR strtolower(reset($this->attr['method'])) == 'get'))	
		{
			$this->add(new FormrHidden(array(
                    'name'    => $CI->security->get_csrf_token_name(),
                    'value'   => $CI->security->get_csrf_hash(),
               )));
		}
		
		$html .= '<fieldset>';
		
		// Loop though the inputs
		if (count($this->segments) > 0) :
               foreach($this->segments as $obj) :
                    $html .= $obj->_export();
                    $html .= PHP_EOL;
               endforeach;
          endif;
          
		$html .= '</fieldset>';
          $html .= '</form>';
          
          // Filter the Results
          if (! is_null($this->filter))
          {
               $html = call_user_func($this->filter, $html);
          }
          
          $this->rendered = $html;
     }
     
     /**
      * Echo's the Rendered Data
      * Requires you to render it first
      *
      * @access     public
      * @return     void
     **/
     public function display()
     {
          echo $this->rendered;
     }
     
     /**
      * Return the data
      * Requires you to render it first
      *
      * @access     public
      * @return     string
     **/
     public function rtn()
     {
          return $this->rendered;
     }
}

/**
 * Input Methods
 * This is the abstract class that will be extended.
 *
 * @access     private
 * @package    Formr
**/
abstract class FormrInputBase
{
     public $attr = array();
     
     public $info = array();
     
     /**
      * Set a Title
      *
      * @access     public
      * @param      string
     **/
     public function title($str )
     {
          $this->info['title'] = $str;
     }
     
     /**
      * Set a Help Block Text
      *
      * @access     public
      * @param      string
     **/
     public function help_block($str )
     {
          $this->info['help_block'] = $str;
     }
     
     /**
      * Set a Element Attribute
      *
      * You can have multiple values on the attributes. You can pass
      * $override and it will set the $val to be the only value to that
      * attribute.
      *
      * For ID, we will force an override.
      *
      * @access     public
      * @param      string
      * @param      string
      * @param      bool To override the existing attributes
     **/
     public function attr($key, $val, $override = false)
     {
          $key = trim($key);
          $val = trim($val);
          $override = (bool) $override;
          
          // If it's 'el_title', it's not an attr
          if ($key == 'el_title')
               return $this->title($val);
          
          // Force Override on ID
          if ($key == 'id' OR $key == 'ID')
               $override = TRUE;
          
          if ($override)
               return $this->attr[$key] = array($val);
          else
               return $this->attr[$key][] = $val;
     }
     
     /**
      * Set the Form Method
      *
      * @access     public
      * @param      string
     **/
     public function set_method($type = 'POST')
     {
          return $this->attr('method', strtoupper($TYPE), TRUE);
     }
     
     /**
      * Using the __call() method to easily apply attributes
      *
      * So calling ->class('what') will set the attribute of class.
      *
      * @param      string The method
      * @param      array The arguments
     **/
     public function __call($method, $args = array())
     {
          call_user_func_array(array($this, 'attr'), array($method, $args[0]));
     }
     
     /**
      * Wrap the Input Tag in Twitter Boostrap Formatting
      *
      * @param      string
      * @return     string
     **/
     public function add_input_container($string)
     {
          $the_id = (isset($this->attr['id'])) ? reset($this->attr['id']) : NULL;
          
          $out = '<div class="clearfix">';
          
          // Label
          if (! is_null($the_id))
               $out .= '<label for="'.$the_id.'">';
          else
               $out .= '<label>';
          
          // Title
          $out .= $this->info['title'];
          
          // End of label
          $out .= '</label>';
          
          // Begin the actual text container
          $out .= '<div class="input">';
          $out .= $string;
          $out .= '</div></div>';
          
          return $out;
     }
     
     /**
      * Get the value from POST or the default value
      *
      * @param      string The ID
      * @param      string The default value
     **/
     public function post($id, $default = '')
     {
          if (isset($_POST[$id]))
               return $_POST[$id];
          else
               return $default;
     }
}

/**
 * Text Input
 *
 * This Generates the input only, not the label and the container.
 *
 * @access     public
 * @package    Formr
**/
class FormrInput extends FormrInputBase
{
     /**
      * Constructor
      *
      * @access     public
      * @param      array
     **/
     public function __construct($args = array())
     {
          $args = (array) $args;
          
          if (count($args) == 0) return;
          
          foreach($args as $key => $val) :
               if (is_array($val))
                    $this->attr($key, $val['value'], ((isset($val['override'])) ? $val['override'] : FALSE));
               else
                    $this->attr($key, $val);
          endforeach;
     }
     
     /**
      * Internal Export Function
      *
      * @access     private
      * @return     mixed
     **/
     public function _export()
     {
          $out = '<input ';
          
          if (count($this->attr) > 0) :
               foreach($this->attr as $key => $val) :
                    
                    if ($key == 'value')
                         $val = $this->post($key, $val);
                    
                    $out .= $key.'="'.implode(' ', $val).'" ';
               endforeach;
          endif;
          
          // The Type
          if (! isset($this->attr['type']))
               $out .= 'type="text" ';
          
          $out .= '/>';
          
          return $this->add_input_container($out);
     }
}

/**
 * Text Input Prepend
 *
 * This Generates the input only, not the label and the container.
 *
 * @access     public
 * @package    Formr
**/
class FormrInputPrepend extends FormrInputBase
{
     public $prepend = '';
     
     /**
      * Constructor
      *
      * @access     public
      * @param      array
     **/
     public function __construct($args)
     {
          $args = (array) $args;
          
          if (count($args) == 0) return;
          
          foreach($args as $key => $val) :
               if ($key == 'prepend') :
                    $this->prepend = $val;
                    continue;
               endif;
               
               if (is_array($val))
                    $this->attr($key, $val['value'], ((isset($val['override'])) ? $val['override'] : FALSE));
               else
                    $this->attr($key, $val);
          endforeach;
     }
     
     /**
      * Internal Export Function
      *
      * @access     private
      * @return     mixed
     **/
     public function _export()
     {
          $out = '<div class="input-prepend"><span class="add-on">'.$this->prepend.'</span><input ';
          
          if (count($this->attr) > 0) :
               foreach($this->attr as $key => $val) :
                    
                    if ($key == 'value')
                         $val = $this->post($key, $val);
                    
                    $out .= $key.'="'.implode(' ', $val).'" ';
               endforeach;
          endif;
          
          // The Type
          if (! isset($this->attr['type']))
               $out .= 'type="text" ';
          
          $out .= '/></div>';
          
          return $this->add_input_container($out);
     }
}

/**
 * Text Input Apend
 *
 * This Generates the input only, not the label and the container.
 *
 * @access     public
 * @package    Formr
**/
class FormrInputAppend extends FormrInputBase
{
     public $append = '';
     
     /**
      * Constructor
      *
      * @access     public
      * @param      array
     **/
     public function __construct($args)
     {
          $args = (array) $args;
          
          if (count($args) == 0) return;
          
          foreach($args as $key => $val) :
               if ($key == 'append') :
                    $this->append = $val;
                    continue;
               endif;
               
               if (is_array($val))
                    $this->attr($key, $val['value'], ((isset($val['override'])) ? $val['override'] : FALSE));
               else
                    $this->attr($key, $val);
          endforeach;
     }
     
     /**
      * Internal Export Function
      *
      * @access     private
      * @return     mixed
     **/
     public function _export()
     {
          $out = '<div class="input-append"><input ';
          
          if (count($this->attr) > 0) :
               foreach($this->attr as $key => $val) :
                    
                    if ($key == 'value')
                         $val = $this->post($key, $val);
                    
                    $out .= $key.'="'.implode(' ', $val).'" ';
               endforeach;
          endif;
          
          // The Type
          if (! isset($this->attr['type']))
               $out .= 'type="text" ';
          
          $out .= '/><span class="add-on">'.$this->append.'</span></div>';
          
          return $this->add_input_container($out);
     }
}

/**
 * Textarea
 *
 * This Generates the input only, not the label and the container.
 *
 * @access     public
 * @package    Formr
**/
class FormrTextarea extends FormrInputBase
{
     /**
      * Constructor
      *
      * @access     public
      * @param      array
     **/
     public function __construct($args)
     {
          $args = (array) $args;
          
          if (count($args) == 0) return;
          
          foreach($args as $key => $val) :
               if (is_array($val))
                    $this->attr($key, $val['value'], ((isset($val['override'])) ? $val['override'] : FALSE));
               else
                    $this->attr($key, $val);
          endforeach;
     }
     
     /**
      * Internal Export Function
      *
      * @access     private
      * @return     mixed
     **/
     public function _export()
     {
          $out = '<textarea ';
          
          if (count($this->attr) > 0) :
               foreach($this->attr as $key => $val) :
                    
                    // The value goes inside the textarea tags
                    if ($key !== 'value')
                         $out .= $key.'="'.implode(' ', $val).'" ';
               endforeach;
          endif;
          
          $out .= '>';
          
          // Value
          if (isset($this->attr['value']))
               $out .= reset($this->attr['value']);
          
          $out .= '</textarea>';
          
          return $this->add_input_container($out);
     }
}

/**
 * Hidden Input
 *
 * This creates a hidden input
 *
 * @access     public
 * @package    Formr
**/
class FormrHidden extends FormrInputBase
{
     /**
      * Constructor
      *
      * @access     public
      * @param      array
     **/
     public function __construct($args)
     {
          $args = (array) $args;
          
          if (count($args) == 0) return;
          
          foreach($args as $key => $val) :
               if (is_array($val))
                    $this->attr($key, $val['value'], ((isset($val['override'])) ? $val['override'] : FALSE));
               else
                    $this->attr($key, $val);
          endforeach;
          
          // Set it hidden
          $this->attr('type', 'hidden');
     }
     
     /**
      * Internal Export Function
      *
      * @access     private
      * @return     mixed
     **/
     public function _export()
     {
          $out = '<input ';
          
          if (count($this->attr) > 0) :
               foreach($this->attr as $key => $val)
                    $out .= $key.'="'.implode(' ', $val).'" ';
          endif;
          
          $out .= '/>';
          
          return $out;
     }
}

/**
 * Formr Legend
 *
 * @access     public
 * @param      string
**/
class FormrLegend
{
     private $n = '';
     private $close_fieldset = FALSE;
     
     public function __construct($w, $close_fieldset = false)
     {
          $this->n = $w;
          $this->close_fieldset = $close_fieldset;
     }
     
     /**
      * Export a Legend
      *
      * @access     public
     **/
     public function _export()
     {
          return (($this->close_fieldset) ? '</fieldset><fieldset>' : '' ) . '<legend>'.$this->n.'</legend>';
     }
}

/**
 * Custom HTML
 *
 * @access     public
 * @param      object A Callable Function
**/
class FormrCustom extends FormrInputBase
{
     public $o = '';
     
     public function __construct($w = '')
     {
          $this->o = $w;
     }
     
     /**
      * Export a nifty HTML block
      *
      * @access     public
     **/
     public function _export()
     {
          if (! is_callable($this->o))
               return $this->add_input_container($this->o);
          else
               return call_user_func(array($this, 'o'));
     }
}

/**
 * Actions and Input
 *
 * @access     public
 * @package    Core
**/
class FormActions
{
     private $arg = array();
     
     public function __construct($args = array())
     {
          $this->arg = $args;
     }
     
     /**
      * Export
     **/
     public function _export()
     {
          $out = '<div class="actions">';
          
          if (isset($this->arg['primary']))
          {
               $primary = $this->arg['primary'];
               
               $out .= '<input ';
               
               if ( ! isset($primary['type']))
                    $primary['type'] = array('submit');
               
               if (! isset($primary['class']))
                    $primary['class'] = array('btn primary');
               
               foreach($primary as $key => $val) :
                    if (! is_array($val))
                         $out .= $key.'="'.$val.'" ';
                    else
                         $out .= $key.'="'.implode(' ', $val).'" ';
               endforeach;
               
               $out .= '/>';
          }
          
          if (isset($this->arg['secondary']))
          {
               $primary = $this->arg['secondary'];
               
               $out .= '<input ';
               
               if ( ! isset($primary['type']))
                    $primary['type'] = array('button');
               
               if (! isset($primary['class']))
                    $primary['class'] = array('btn');
               
               foreach($primary as $key => $val) :
                    if (! is_array($val))
                         $out .= $key.'="'.$val.'" ';
                    else
                         $out .= $key.'="'.implode(' ', $val).'" ';
               endforeach;
               
               $out .= '/>';
          }
          
          $out .= '</div>';
          
          return $out;
     }
}

class FormrHr
{
     public function _export()
     {
          return '<hr />';
     }
}

/**
 * District Drop down
 * Make it so that the user can select a district from a drop down menu
 *
 * @access     public
 * @param      int The district ID to select
 * @param      bool Can we select another one? (defaults to true)
 * @param      string You can add some string to the '<select>' tag
 * @return     void
**/
function formr_district_dropdown($selected = -1, $can_select = true, $add = '')
{
     // Query
     $get = get_instance()->db->from('district')
     ->where('isDeleted', 0)
     ->order_by('district_name', 'asc')
     ->get();
     
     $out = '<select name="districts" id="districts" ';
     
     if (! $can_select)
          $out .= 'disabled="disabled"';
     
     $out .= '>';
     
     // Default one
     $out .='<option value="-1" ';
     if ($selected < 0)
          $out .= 'selected="selected"';
     
     $out .= '>Select a District</option>';
     
     if ($get->num_rows() > 0) : foreach($get->result() as $row) : 
          $out .='<option value="'.$row->id.'"';
          
          if ($row->id == $selected)
               $out .= 'selected="selected';
          $out .= '>'.$row->district_name.'</option>';
     endforeach; endif;
     $out .= '</select>';
     
     return $out;
}

/* End of file Formr.php */