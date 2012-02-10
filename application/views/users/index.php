<?php $this->load->view('shared/spotlight', array('title' => 'View Users')); ?>
<?php
/**
 * Made it a function so it isn't a hassle to remake it multiple times
 * @ignore
**/
function users_table($data, $can_manage)
{
     ?><thead>
     <tr>
          <th class="header">#</th>
          <th class="yellow header headerSortDown">Name</th>
          <th class="blue header">Email</th>
          <th class="green header">&nbsp;</th>
     </tr></thead>
     
     <tbody>
          <?php if (count($data) == 0) : ?>
               <tr><td colspan="4">
                    <div class="alert-message warning">
        <p><strong>Whoops!</strong> We can't find any users for to fill this table.</p>
      </div>
               </td></tr>
          <?php else : foreach( $data as $row ) : ?>
          <tr>
               <td><?php echo ($can_manage) ? anchor('users/edit/'.$row->user_id, number_format($row->user_id)) : number_format($row->user_id); ?></td>
               <td><?php echo ($can_manage) ? anchor('users/edit/'.$row->user_id, $row->user_first_name . ' '. $row->user_last_name) : $row->user_first_name . ' '. $row->user_last_name; ?></td>
               <td><?php echo (empty($row->user_email)) ? 'n/a' : $row->user_email; ?></td>
               <td class="alignRight"><?php if ($can_manage) :
               
               // Edit Button
               echo anchor('users/edit/'.$row->user_id, 'Edit', array('class' => 'btn'));
               echo nbs(1);
               
               $m = new Modal();
               $m->title('Are you sure you want to delete this?');
               
               if ($row->user_id == Auth::uid())
                    $m->content('<div class="alert-message error alignCenter">This <strong>will</strong> delete your user account and you won\'t be able to access the system.</div>');
               else
                    $m->content('There is no going back, unless you re-add the user.');
               
               $m->primary('Yes, I\'m Sure', site_url('users/delete/'.$row->user_id.'/'.md5($row->user_id . date('o'))), 'btn error');
               //$m->secondary('Go Back', )
               $m->link('Delete', 'btn error '.(($row->user_id == Auth::uid()) ? 'disabled' : ''), '', (($row->user_id == Auth::uid()) ? 'disabled="disabled"' : ''));
               
               // Render+Display
               $m->render();
               $m->display();
               
               else :
                    echo nbs(1);
               endif; ?></td>
          </tr>
          <?php endforeach; endif; ?>
     </tbody><?php
}
?>
<!-- Content Text -->
<div class="container white-container extra-padding">
     <div class="row">
          <div class="span14 columns">
               <div class="page-header"><h2>View Users
               <?php if (Perm::has_perk('users.create-teacher')) : ?>
               <a href="<?php echo site_url('users/add'); ?>" class="btn primary right">Add User</a>
               <?php endif; ?></h2></div>
               
               <?php
               // Admins
               if (Perm::perk_contents('global.level') == 1) :
               
               if (Perm::perk_contents('global.level') == 1)
                    $can_manage = TRUE;
               else
                    $can_manage = FALSE;
                    ?>
                    <h3>Admins</h3><table class="zebra-striped" id="adminDataTable">
                    <?php users_table($admins, $can_manage); ?>
                    </table><?php
               endif;
               
               // District Admins
               if (Perm::has_perk('users.manage-district_admin'))
                    $can_manage = TRUE;
               else
                    $can_manage = FALSE;
                    ?>
                    <h3>District Admins</h3><table class="zebra-striped" id="DistrctAdminDataTable">
                    <?php users_table($district_admins, $can_manage); ?>
                    </table><?php
               
               // Principals
               if (Perm::has_perk('users.manage-school_admin'))
                    $can_manage = TRUE;
               else
                    $can_manage = FALSE;
                    ?>
                    <h3>School Admins</h3><table class="zebra-striped" id="schoolAdminDataTable">
                    <?php users_table($school_admins, $can_manage); ?>
                    </table><?php
               
               // teachers
               if (Perm::has_perk('users.manage-teacher'))
                    $can_manage = TRUE;
               else
                    $can_manage = FALSE;
                    ?>
                    <h3>Teachers</h3><table class="zebra-striped" id="teachersDataTable">
                    <?php users_table($teachers, $can_manage); ?>
                    </table><?php
               
               ?>
             
          </div>
          
          <!-- Right column -->
          <div class="span4 columns">



<?php //if ($school_row->id !== Auth::get('school_id')) : ?>
<h3>Questions?</h3>
<p>Shoot an email to <a href="mailto:support@truanttoday.com">support@truanttoday.com</a>.</p>

<p><small>
<?php
$p = new Popover();
$p->placement('right');
$p->title('This is not a toy!');
$p->content('This will allow you to delete yourself - if you even can. Don\'t touch this if you don\'t know what you\'re doing.');
$p->link('Allow to Delete Yourself', '', '#', 'id="allow-delete-self"');

$p->render();
$p->display();
?>
</small></p>
<?php //endif; ?>

          </div>
     </div>
</div>

<?php
/**
 * Add to footer
 *
 * @access     private
 * @ignore
**/
function add_to_class_foot()
{
     ?>
     // Table sorting
     $("table#adminDataTable").tablesorter({ sortList: [[1,0]] });
     $("table#DistrctAdminDataTable").tablesorter({ sortList: [[1,0]] });
     $("table#schoolAdminDataTable").tablesorter({ sortList: [[1,0]] });
     $("table#teachersDataTable").tablesorter({ sortList: [[1,0]] });
     $("table#parentsDataTable").tablesorter({ sortList: [[1,0]] });
     
     
     $('a#allow-delete-self').click(function()
     {
          $('a.btn[disabled]').removeClass('disabled').removeAttr('disabled');
          $(this).remove();
          $('div.popover').remove();
     });
     <?php
}

add_action('js_footer', 'add_to_class_foot');