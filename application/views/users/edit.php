<?php $this->load->view('shared/spotlight', array('title' => ($user_row->user_id == Auth::uid()) ? 'Edit My Profile' : 'Edit a User')); ?>

<!-- Content Text -->
<div class="container white-container extra-padding">
     <div class="row">
          <div class="span13 columns">
               <div class="page-header"><h2>Edit User <small><?php echo $user_row->user_first_name . ' ' . $user_row->user_last_name; ?>
               <?php if (Bill::is_district_active()) : ?>
               <a href="<?php echo site_url('users'); ?>" class="btn   right">Back to Users</a>
               <?php endif; ?>
               </small></h2></div>
               
               <?php echo form_open('users/edit/'.$theID); ?>
               <fieldset>
                    <div class="clearfix">
                         <label for="login_name">Login Name</label>
                         <div class="input"><input name="login_name" type="text" autocomplete="off" id="login_name" class="" placeholder="" value="<?php echo set_value('login_name', $user_row->user_slug); ?>" /></div>
                    </div>
                    <div class="clearfix">
                         <label for="email">Email</label>
                         <div class="input"><input name="email" autocomplete="off" type="text" id="email" class="" value="<?php echo set_value('email', $user_row->user_email); ?>" /></div>
                    </div>
                    
                    <div class="clearfix">
                         <label for="password">Password</label>
                         <div class="input"><input name="password" autocomplete="off" type="password" id="password" placeholder="Leave blank if not changing" class="" value="<?php echo set_value('password'); ?>" /></div>
                    </div>
                    
                    <div class="clearfix">
                         <label for="role">Role</label>
                         <div class="input">
                              <?php if ( ! Perm::has_perk('users.create-teacher')) : ?>
                                   <span class="uneditable-input"><?php echo ucwords($user_row->role); ?></span>
                              <?php else : ?>
                              <select name="role" id="role">
                                   <?php if (Perm::has_perk('users.create-admin')) : ?><option value="admin" <?php if ($user_row->role == 'admin') echo 'selected="selected"'; ?>>Admin</option><?php endif; ?>
                                   <?php if (Perm::has_perk('users.create-district_admin')) : ?><option value="district_admin" <?php if ($user_row->role == 'district_admin') echo 'selected="selected"'; ?>>District Admin</option><?php endif; ?>
                                   <?php if (Perm::has_perk('users.create-school_admin')) : ?><option value="school_admin" <?php if ($user_row->role == 'school_admin') echo 'selected="selected"'; ?>>School Admin</option><?php endif; ?>
                                   <?php if (Perm::has_perk('users.create-teacher')) : ?><option value="teacher" <?php if ($user_row->role == 'teacher') echo 'selected="selected"'; ?>>Teacher</option><?php endif; ?>
                              </select>
                              <?php endif; ?>
                         </div>
                    </div>
                    
                    <?php
                    // Hide from those who can't edit
                    if (Perm::perk_contents('global.level') < 2) : ?>
                    
                    <div class="clearfix">
                         <label for="school">School</label>
                         <div class="input">
                              <select name="school" id="school">
                                   <?php $schools = $this->db->from('school')->where('isDeleted', 0)->order_by('school_name', 'asc')->get(); foreach($schools->result() as $school) : ?>
                                   <option value="<?php echo $school->id; ?>" <?php if ($user_row->school_id == $school->id) echo 'selected="selected"'; ?>><?php echo $school->school_name; ?></option>
                                   <?php endforeach; ?>
                              </select>
                         </div>
                    </div>
                    <?php endif; ?>
                    <hr />
                    <div class="clearfix">
                         <label for="first_name">First Name</label>
                         <div class="input"><input name="first_name" type="text" id="first_name" class="xlarge" value="<?php echo set_value('first_name', $user_row->user_first_name); ?>" /></div>
                    </div>
                    
                    <div class="clearfix">
                         <label for="last_name">Last Name</label>
                         <div class="input"><input name="last_name" type="text" id="last_name" class="xlarge" value="<?php echo set_value('last_name', $user_row->user_last_name); ?>" /></div>
                    </div>
                    
                    
                    <div class="actions">
                         <input type="submit" class="btn primary" value="Update User" />&nbsp;<button type="reset" class="btn">Cancel</button>
                    </div>
               </fieldset>
               <?php echo form_close(); ?>             
          </div>
          
          <div class="span5 columns">
               
               <h3>Send User New Password</h3>
               <a href="<?php echo site_url('users/reset_password/'.$theID.'/'. md5($theID . date('o'))); ?>" class="btn large primary">Send Password</a>
               <h3>Why can't I add a parent?</h3>
               <p>You can't add a parent because they have to be assigned to a single student and can only be added via the "<a href="<?php echo site_url('students'); ?>">Students</a>" page.</p>
               
               <?php if (Auth::uid() == $theID) : ?>
                         <hr />
                         <center><a href="<?php echo site_url('feedback'); ?>" class="btn">User Feedback</a></center>
               <?php endif; ?>
          </div>
          
     </div>
</div>