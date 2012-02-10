<?php $this->load->view('shared/spotlight', array('title' => 'Add a User')); ?>

<!-- Content Text -->
<div class="container white-container extra-padding">
     <div class="row">
          <div class="span13 columns">
               <div class="page-header"><h2>Add a Users <small><a href="<?php echo site_url('users'); ?>" class="btn   right">Back to Users</a></small></h2></div>
               <p>By adding an account, we're going to email the password for this account to the email provided below. You can always resend and reset this password in the future.</p>
               <?php echo form_open('users/add'); ?>
               <fieldset>
                    <div class="clearfix">
                         <label for="login_name">Login Name</label>
                         <div class="input"><input name="login_name" type="text" id="login_name" class="" placeholder="Will default to your email..." value="<?php echo set_value('login_name'); ?>" /></div>
                    </div>
                    <div class="clearfix">
                         <label for="email">Email</label>
                         <div class="input"><input name="email" type="text" id="email" class="" value="<?php echo set_value('email'); ?>" /></div>
                    </div>
                    
                    <div class="clearfix">
                         <label for="password">Password</label>
                         <div class="input"><input name="password" type="password" id="password" placeholder="We'll generate it if empty..." class="" value="<?php echo set_value('password'); ?>" /></div>
                    </div>
                    
                    <div class="clearfix">
                         <label for="role">Role</label>
                         <div class="input">
                              <select name="role" id="role">
                                   <?php if (Perm::has_perk('users.create-admin')) : ?><option value="admin">Admin</option><?php endif; ?>
                                   <?php if (Perm::has_perk('users.create-district_admin')) : ?><option value="district_admin">District Admin</option><?php endif; ?>
                                   <?php if (Perm::has_perk('users.create-school_admin')) : ?><option value="school_admin">School Admin</option><?php endif; ?>
                                   <?php if (Perm::has_perk('users.create-teacher')) : ?><option value="teacher">Teacher</option><?php endif; ?>
                              </select>
                         </div>
                    </div>
                    
                    <?php if (Perm::has_perk('users.create-district_admin')) : ?>
                    <div class="clearfix">
                         <label for="school">School</label>
                         <div class="input">
                              <select name="school" id="school">
                                   <?php
                                   $this->db->from('school');
                                   $this->db->where('isDeleted', 0);
                                   $this->db->order_by('school_name', 'asc');
                                   
                                   // Permissions
                                   if (Perm::has_perk('users.create-admin')) :
                                        // Unlimited
                                        
                                   else :
                                        $this->db->where('district_id', Auth::get('district_id'));
                                   endif;
                                   
                                   $schools = $this->db->get();
                                   
                                   foreach($schools->result() as $school) : ?>
                                   <option value="<?php echo $school->id; ?>"><?php echo $school->school_name; ?></option>
                                   <?php endforeach; ?>
                              </select>
                         </div>
                    </div>
                    
                    <?php else : // We force them to have a school ?>
                         <input  type="hidden" name="school" value="<?=Auth::get('school_id')?>" />
                    <?php endif; ?>
                    <hr />
                    <div class="clearfix">
                         <label for="first_name">First Name</label>
                         <div class="input"><input name="first_name" type="text" id="first_name" class="xlarge" value="<?php echo set_value('first_name'); ?>" /></div>
                    </div>
                    
                    <div class="clearfix">
                         <label for="last_name">Last Name</label>
                         <div class="input"><input name="last_name" type="text" id="last_name" class="xlarge" value="<?php echo set_value('last_name'); ?>" /></div>
                    </div>
                    
                    
                    <div class="actions">
                         <input type="submit" class="btn primary" value="Create User" />&nbsp;<button type="reset" class="btn">Cancel</button>
                    </div>
               </fieldset>
               <?php echo form_close(); ?>             
          </div>
          
          <div class="span5 columns">
               <h3>Why can't I add a parent?</h3>
               <p>You can't add a parent because they have to be assigned to a single student and can only be added via the "<a href="<?php echo site_url('students'); ?>">Students</a>" page.</p>
          </div>
          
     </div>
</div>