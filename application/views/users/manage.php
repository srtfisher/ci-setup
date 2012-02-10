<?php $is_disabled = (! Perm::has_perk('periods.manage')) ? TRUE : FALSE; ?>
<?php $this->load->view('shared/spotlight', array('title' => ($is_disabled) ? 'View Period' : 'Manage Period')); ?>

<!-- Content Text -->
<div class="container white-container extra-padding">
     <div class="row">
          <div class="span13 columns">
               <div class="page-header"><h2><?php if ($is_disabled) echo 'View'; else echo 'Manage'; ?> Period</h2></div>
               
               <?php if ($is_disabled) : ?>
                    <div class="alert-message warning">
        <p><strong>Just a Quick Note:</strong> You don't have permissions to edit, but you can view the period.</p>
      </div>
               <?php endif; ?>
               
               <?php echo form_open('periods/manage/'.$theID); ?>
               <fieldset>
                    <div class="clearfix">
                         <label for="period_name">Period Name</label>
                         <div class="input"><input name="period_name" type="text" id="period_name" class="xlarge <?php if ($is_disabled) echo 'disabled'; ?>" value="<?php echo set_value('period_name', $period_row->period_name); ?>" /></div>
                    </div>
                    
                   <div class="clearfix">
                         <label for="period_start_time">Start Time</label>
                         <div class="input"><input name="period_start_time" type="text" id="period_start_time" class="xlarge <?php if ($is_disabled) echo 'disabled'; ?>" value="<?php echo set_value('period_start_time', $period_row->period_start_time); ?>" /></div>
                    </div>
                    <div class="clearfix">
                         <label for="period_end_time">End Time</label>
                         <div class="input"><input name="period_end_time" type="text" id="period_end_time" class="xlarge <?php if ($is_disabled) echo 'disabled'; ?>" value="<?php echo set_value('period_end_time', $period_row->period_end_time); ?>" /></div>
                    </div>
                    
                    <div class="clearfix">
                         <label for="monday_bool">Monday</label>
                         <div class="input">
                         <?php
                         echo form_checkbox(array(
                             'name'        => 'monday_bool',
                             'id'          => 'monday_bool',
                             'value'       => '1',
                             'checked'     => ($period_row->monday_bool) ? TRUE : FALSE,
                        )); ?> Yes</div>
                    </div>
                    <div class="clearfix">
                         <label for="tuesday_bool">Tuesday</label>
                         <div class="input"><?php
                         echo form_checkbox(array(
                             'name'        => 'tuesday_bool',
                             'id'          => 'tuesday_bool',
                             'value'       => '1',
                             'checked'     => ($period_row->tuesday_bool) ? TRUE : FALSE,
                        )); ?> Yes</div>
                    </div>
                    <div class="clearfix">
                         <label for="wednesday_bool">Wednesday</label>
                         <div class="input"><?php
                         echo form_checkbox(array(
                             'name'        => 'wednesday_bool',
                             'id'          => 'wednesday_bool',
                             'value'       => '1',
                             'checked'     => ($period_row->wednesday_bool) ? TRUE : FALSE,
                        )); ?> Yes</div>
                    </div>
                    <div class="clearfix">
                         <label for="thursday_bool">Thursday</label>
                         <div class="input"><?php
                         echo form_checkbox(array(
                             'name'        => 'thursday_bool',
                             'id'          => 'thursday_bool',
                             'value'       => '1',
                             'checked'     => ($period_row->thursday_bool) ? TRUE : FALSE,
                        )); ?> Yes</div>
                    </div>
                    <div class="clearfix">
                         <label for="friday_bool" title=":)">Friday</label>
                         <div class="input"><?php
                         echo form_checkbox(array(
                             'name'        => 'friday_bool',
                             'id'          => 'friday_bool',
                             'value'       => '1',
                             'checked'     => ($period_row->friday_bool) ? TRUE : FALSE,
                        )); ?> Yes</div>
                    </div>
                    <?php if (Perm::has_perk('schools.manage')) : ?>
                    <div class="actions">
                         <input type="submit" class="btn primary" value="Save Changes" />&nbsp;<button type="reset" class="btn">Cancel</button>
                    </div>
                    <?php endif; ?>
               </fieldset>
               <?php echo form_close(); ?>             
          </div>
          
          <!-- Right column -->
          <div class="span5 columns">
<h3>What timezone?</h3>
<p>They're based in the timezone selected for the district this school is in.</p>

<h3>Classes in this Period</h3><?php
$get_classes = $this->db->from('classes')->where('period_id', $period_row->id)->get();
if ($get_classes->num_rows() == 0) :
?>
<div class="alert-message block-message warning">
        <p><strong>No classes found!</strong></p>
      </div>
<?php
else : foreach($get_classes->result() as $row) :  ?>
<div class="padded-bottom-border">
     <h5><?php echo $row->class_name; ?></h5>
</div>
<?php endforeach; endif; ?>
<p>&nbsp;</p>
<h3>How do I delete a period?</h3>
<div class="alert-message block-message error">
        <p><strong>You can easily delete a period!</strong> If you have any classes assigned to this period, you can move all the classes to another period. 
        If you have classes and no other period to move them to, you <strong>can't</strong> delete this period!</p>
        
          <div class="alert-actions">
          <?php
          // Have classes
          // Why query again!
          $get_classes = $this->db->from('classes')->where('period_id', $period_row->id)->get();
          if ($get_classes->num_rows() > 0)
          {
               // Now, do they have periods to move it to?
               $have_periods = $this->db->from('period')->where('school_id', $school_row->id)->get();
               if ($have_periods->num_rows() > 0)
               {
                    // They have the classes to move them to
                    ?><form action="<?php echo site_url('periods/delete/'.$theID); ?>" method="post">
                    <select name="periods_move" id="periods_move">
                         <?php foreach($have_periods->result() as $row) : ?>
                              <option value="<?php echo $row->id; ?>"><?php echo $row->period_name; ?></option>
                         <?php endforeach; ?>
                    </select>
                    <input type="submit" value="Move + Delete" class="btn large" style="margin-top:5px;" />
                    </form><?php
               }
               else
               {
                    // No go!
                    ?><em>You don't have periods to move the classes to!</em><?php
               }
          }
          else
          {
               // No classes! :)
               ?><form action="<?php echo site_url('periods/delete/'.$theID); ?>" method="post">
                    <input type="submit" class="btn large" value="Delete Period" />
               </form>
               <?php
          }
          ?>
          </div>
      </div>
          </div>
     </div>
</div>