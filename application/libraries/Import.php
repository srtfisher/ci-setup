<?php
/**
 * The importer class to import data from CSV and other formats
 *
 * @access     public
 * @package    Core
 * @author     srtfisher
**/
class Import
{
     /**
      * The constructor
      *
      * @param      void
     **/
     public function __construct()
     {
          // Nada
     }
     
     /**
      * Parse a CSV file for sending messages for a class
      *
      * @access     public
      * @param      string
      * @param      object
      * @param      object
     **/
     public static function parse_class_file($file, $class_row, $student_in_class)
     {
          require_once(dirname(__FILE__).DS.'parsecsv'.EXT);
          
          $csv = new parseCSV();
          $csv->auto($file);
          
          // In error
          if ($csv->error > 1)
               return simple_error('There was an error importing that CSV. Make sure it\'s the right format and try again.');
          
          $data = $csv->data;
          $msg = array();
          
          if (count($data) > 0) : foreach($data as $row) :
               if (isset($row['student_id']))
               {
                    foreach($student_in_class->result() as $single_student) :
                         if ($single_student->school_issued_id == $row['student_id'])
                         {
                              $send = (! isset($row['send']) OR $row['send'] == 'no' OR ! $row['send']) ? FALSE : TRUE;
                              $in_attendance = (! isset($row['status']) OR $row['status'] == 'absent' OR ! $row['status']) ? FALSE : TRUE;
                              
                              $msg[] = get_instance()->Contact->process($single_student, $class_row->id, $in_attendance, $send);
                         }
                    endforeach;
               }
          endforeach; endif;
          
          return $msg;
     }
     
     /**
      * Parse a CSV file for importing students
      *
      * @access     public
      * @param      string The Full file path to the CSV
      * @param      object School DB row
     **/
     public static function parse_students($file, $school_row)
     {
          require_once(dirname(__FILE__).DS.'parsecsv'.EXT);
          
          $csv = new parseCSV();
          $csv->auto($file);
          
          // In error
          if ($csv->error > 1)
               return simple_error('There was an error importing that CSV. Make sure it\'s the right format and try again.');
          
          $data = $csv->data;
          $msg = array();
          
          // We need to validate the data
          if (count($data) == 0)
               return simple_error('That CSV file is empty.');
          
          // Sample it
          $sample = reset($data);
          if (! isset($sample['student_first_name']) OR ! isset($sample['student_last_name']))
               return simple_error('That CSV file doesn\'t contain a first name or last name row.');
          
          $CI = get_instance();
          
          foreach($data as $row) :
               $CI->db->set('school_id', $school_row->id);
               $CI->db->set('school_issued_id', ((isset($row['student_school_id'])) ? intval($row['student_school_id']) : 0));
               $CI->db->set('student_first_name', trim($row['student_first_name']));
               $CI->db->set('student_last_name', trim($row['student_last_name']));
               $CI->db->set('student_year', ((isset($row['student_year'])) ? $row['student_year'] : ''));
               $CI->db->set('create_time', current_datetime());
               $CI->db->set('update_user', Auth::uid());
               $CI->db->insert('student');
               
               $msg[] = 'Created user #'.$CI->db->insert_id().' for "'.$row['student_first_name'].' '. $row['student_last_name'].'"';
          endforeach;
          
          return $msg;
     }
}
/* End of file Import.php */