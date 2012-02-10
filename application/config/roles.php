<?php
/**
 * User Permissions Setup
 *
 * Perks are things you can test if a user has. You can test if they have a certain perk,
 * which means they can do a certain item.
 *
 * @package    Core
 * @global     array
**/
$config['permissions'] = array(
     
     /*
     The setup is:
     {
          'role'    =>   {
                         'perk'    =>   bool
          }
     }
     */
     
     'admin'             => array(
                              // Global permissions item
                              'global.name'            =>   'Site Admin',
                              'global.level'           =>   1,
                              
                              'district.create'        =>   true,
                              'district.manage'        =>   true,
                              'district.delete'        =>   true,
                              
                              // Classes
                              'class.manage'           =>   true,
                              'class.create'           =>   true,
                              'class.delete'           =>   true,
                              'class.students'         =>   true,
                              
                              // This means they can mange the class on a per day basis
                              'class.manage-daily'     =>   true,
                              
                              // Users
                              'users.create-admin'               =>   true,
                              'users.create-district_admin'      =>   true,
                              'users.create-school_admin'        =>   true,
                              'users.create-teacher'             =>   true,
                              'users.create-parent'              =>   true,
                              
                              'users.manage-admin'               =>   true,
                              'users.manage-district_admin'      =>   true,
                              'users.manage-school_admin'        =>   true,
                              'users.manage-teacher'             =>   true,
                              'users.manage-parent'              =>   true,
                              
                              'users.delete-admin'               =>   true,
                              'users.delete-district_admin'      =>   true,
                              'users.delete-school_admin'        =>   true,
                              'users.delete-teacher'             =>   true,
                              'users.delete-parent'              =>   true,
                              
                              // Can always update yourself, right?
                              'users.update-self'                =>   true,
                              
                              // Schools
                              'schools.create'         =>   true,
                              'schools.manage'         =>   true,
                              'schools.delete'         =>   true,
                              'schools.view'           =>   true,
                              
                              // Students
                              'students.manage'        =>   true,
                              'students.delete'        =>   true,
                              'students.add'           =>   true,
                              
                              // Periods
                              'periods.manage'         =>   true,
                              'periods.create'         =>   true,
                              'periods.delete'         =>   true,
                              
                              ),
     
     
     'district_admin'    => array(
                              // Global permissions item
                              'global.name'            =>   'District Admin',
                              'global.level'           =>   2,
                              
                              'district.create'        =>   false,
                              'district.manage'        =>   true,
                              'district.delete'        =>   true,
                              
                              // Classes
                              'class.manage'           =>   true,
                              'class.create'           =>   true,
                              'class.delete'           =>   true,
                              'class.students'         =>   true,
                              
                              // This means they can mange the class on a per day basis
                              'class.manage-daily'     =>   true,
                              
                              // Users
                              'users.create-admin'               =>   false,
                              'users.create-district_admin'      =>   true,
                              'users.create-school_admin'        =>   true,
                              'users.create-teacher'             =>   true,
                              'users.create-parent'              =>   true,
                              
                              'users.manage-admin'               =>   false,
                              'users.manage-district_admin'      =>   true,
                              'users.manage-school_admin'        =>   true,
                              'users.manage-teacher'             =>   true,
                              'users.manage-parent'              =>   true,
                              
                              'users.delete-admin'               =>   false,
                              'users.delete-district_admin'      =>   true,
                              'users.delete-school_admin'        =>   true,
                              'users.delete-teacher'             =>   true,
                              'users.delete-parent'              =>   true,
                              
                              // Can always update yourself, right?
                              'users.update-self'                =>   true,
                              
                              // Schools
                              'schools.create'         =>   true,
                              'schools.manage'         =>   true,
                              'schools.delete'         =>   true,
                              'schools.view'           =>   true,
                              
                              // Students
                              'students.manage'        =>   true,
                              'students.delete'        =>   true,
                              'students.add'           =>   true,
                              
                              // Periods
                              'periods.manage'         =>   true,
                              'periods.create'         =>   true,
                              'periods.delete'         =>   true,
                              ),
     
     'school_admin'      => array(
                              // Global permissions item
                              'global.name'            =>   'School Admin',
                              'global.level'           =>   3,
                              
                              // Classes
                              'class.manage'           =>   true,
                              'class.create'           =>   true,
                              'class.delete'           =>   true,
                              'class.students'         =>   true,
                              
                              // This means they can mange the class on a per day basis
                              'class.manage-daily'     =>   true,
                              
                              // Users
                              'users.create-admin'               =>   false,
                              'users.create-district_admin'      =>   false,
                              'users.create-school_admin'        =>   true,
                              'users.create-teacher'             =>   true,
                              'users.create-parent'              =>   true,
                              
                              'users.manage-admin'               =>   false,
                              'users.manage-district_admin'      =>   false,
                              'users.manage-school_admin'        =>   true,
                              'users.manage-teacher'             =>   true,
                              'users.manage-parent'              =>   true,
                              
                              'users.delete-admin'               =>   false,
                              'users.delete-district_admin'      =>   false,
                              'users.delete-school_admin'        =>   true,
                              'users.delete-teacher'             =>   true,
                              'users.delete-parent'              =>   true,
                              
                              // Can always update yourself, right?
                              'users.update-self'                =>   true,
                              
                              // Schools
                              'schools.create'         =>   false,
                              'schools.manage'         =>   true,
                              'schools.delete'         =>   true,
                              'schools.view'           =>   true,
                              
                              // Students
                              'students.manage'        =>   true,
                              'students.delete'        =>   true,
                              'students.add'           =>   true,
                              
                              // Periods
                              'periods.manage'         =>   true,
                              'periods.create'         =>   true,
                              'periods.delete'         =>   true,
                              ),
     
     'teacher'           => array(
                              // Global permissions item
                              'global.name'            =>   'Teacher',
                              'global.level'           =>   4,
                              
                              // Classes
                              'class.manage'           =>   true,
                              'class.create'           =>   true,
                              'class.delete'           =>   true,
                              'class.students'         =>   true,
                              
                              // This means they can mange the class on a per day basis
                              'class.manage-daily'     =>   true,
                              
                              // Users
                              'users.create-admin'               =>   false,
                              'users.create-district_admin'      =>   false,
                              'users.create-school_admin'        =>   false,
                              'users.create-teacher'             =>   false,
                              'users.create-parent'              =>   true,
                              
                              'users.manage-admin'               =>   false,
                              'users.manage-district_admin'      =>   false,
                              'users.manage-school_admin'        =>   false,
                              'users.manage-teacher'             =>   false,
                              'users.manage-parent'              =>   true,
                              
                              'users.delete-admin'               =>   false,
                              'users.delete-district_admin'      =>   false,
                              'users.delete-school_admin'        =>   false,
                              'users.delete-teacher'             =>   false,
                              'users.delete-parent'              =>   true,
                              
                              // Can always update yourself, right?
                              'users.update-self'                =>   true,
                              
                              // Schools
                              'schools.create'         =>   false,
                              'schools.manage'         =>   false,
                              'schools.delete'         =>   false,
                              'schools.view'           =>   true,
                              
                              // Students
                              'students.manage'        =>   true,
                              'students.delete'        =>   true,
                              'students.add'           =>   true,
                              
                              // Periods
                              'periods.manage'         =>   true,
                              'periods.create'         =>   true,
                              'periods.delete'         =>   true,
                              ),
     
     'parent'            => array(
                              // Global permissions item
                              'global.name'            =>   'Parent/Guardian',
                              'global.level'           =>   5,
                              
                              // Classes
                              'class.manage'           =>   false,
                              'class.create'           =>   false,
                              'class.delete'           =>   false,
                              'class.students'         =>   false,
                              
                              // This means they can mange the class on a per day basis
                              'class.manage-daily'     =>   false,
                              
                              // Users
                              'users.create-admin'               =>   false,
                              'users.create-district_admin'      =>   false,
                              'users.create-school_admin'        =>   false,
                              'users.create-teacher'             =>   false,
                              'users.create-parent'              =>   false,
                              
                              'users.manage-admin'               =>   false,
                              'users.manage-district_admin'      =>   false,
                              'users.manage-school_admin'        =>   false,
                              'users.manage-teacher'             =>   false,
                              'users.manage-parent'              =>   false,
                              
                              'users.delete-admin'               =>   false,
                              'users.delete-district_admin'      =>   false,
                              'users.delete-school_admin'        =>   false,
                              'users.delete-teacher'             =>   false,
                              'users.delete-parent'              =>   false,
                              
                              // Can always update yourself, right?
                              'users.update-self'                =>   true,
                              
                              // Schools
                              'schools.create'         =>   false,
                              'schools.manage'         =>   false,
                              'schools.delete'         =>   false,
                              'schools.view'           =>   false,
                              
                              // Students
                              'students.manage'        =>   false,
                              'students.delete'        =>   false,
                              'students.add'           =>   false,
                              
                              // Periods
                              'periods.manage'         =>   false,
                              'periods.create'         =>   false,
                              'periods.delete'         =>   false,
                              
                              'is_parent'              =>   true,
                              ),
     
     
);
/* End of file roles.php */