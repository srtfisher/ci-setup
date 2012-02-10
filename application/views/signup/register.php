<?php $this->load->view('shared/spotlight', array('title' => 'Signup in Seconds!')); ?>

<!-- Content Text -->
<div class="container white-container extra-padding">

<div class="row">
          <h2 class="landing-page-super">Let's Get Started!</h2>
          <span class="landing-page-super-sub">If you have any questions, we're <a href="<?php echo site_url('app/contact'); ?>">here to help</a>!</span>
          
          <div class="span13 columns">
          <?php echo form_open('signup', array('class' => 'signup-form form-stacked-no')); ?>
               <fieldset>
                    <div class="clearfix">
                         <label for="first-name">First Name</label>
                         <div class="input">
                              <input type="text" class="xlarge" name="first-name" placeholder="John" id="first-name" />
                         </div>
                    </div>
                    
                    <div class="clearfix">
                         <label for="last-name">Last Name</label>
                         <div class="input">
                              <input type="text" class="xlarge" name="last-name" placeholder="Doe" id="last-name" />
                         </div>
                    </div>
                    
                     <div class="clearfix">
                         <label for="your-email">Email</label>
                         <div class="input">
                              <input type="text" class="xlarge" name="your-email" placeholder="john@johndoe.com" id="your-email" />
                         </div>
                    </div>
                    
                    </fieldset>
                    <fieldset class="divider">
                         <legend>Your Login Details</legend><div class="clearfix">
                         
                    <div class="clearfix">
                         <label for="login-name">Login Name</label>
                         <div class="input">
                              <input type="text" class="xlarge" name="login-name" placeholder="" id="login-name" />
                         </div>
                    </div>
                    
                    <div class="clearfix">
                         <label for="your-password">Password</label>
                         <div class="input">
                              <input type="password" class="xlarge" name="your-password" placeholder="" id="your-password" />
                         </div>
                    </div>
                    
                    </fieldset>
                    <fieldset class="divider">
                         <legend>About Your School</legend><div class="clearfix">
                         
                         <label for="district-name">District's Name</label>
                         <div class="input">
                              <input type="text" class="xlarge" name="district-name" id="district-name" />
                         </div>
                    </div>
                    
                    <div class="clearfix">
                         <label for="school-name">School's Name</label>
                         <div class="input">
                              <input type="text" class="xlarge" name="school-name" id="school-name" />
                         </div>
                    </div>
                    <div class="clearfix">
                         <label for="daily_student_funding">Daily Student Funding</label>
                         <div class="input">
                              <div class="input-prepend">
                                   <span class="add-on">$</span>
                                   <input name="daily_student_funding" type="text" size="5" id="daily_student_funding" class="" value="<?php echo set_value('daily_student_funding'); ?>" />
                              </div>
                         </div>
                    </div>
                    
                    <div class="clearfix">
                         <label for="initial_truancy_rate">Initial Truancy Rate</label>
                         <div class="input">
                              <div class="input-append">
                                   <input name="initial_truancy_rate" type="text" size="5" id="initial_truancy_rate" class="" value="<?php echo set_value('initial_truancy_rate'); ?>" />
                                   <span class="add-on">%</span>
                                   
                              </div>
                         </div>
                    </div>
                    <div class="clearfix">
                         <label for="timezones">Timezone</label>
                         <div class="input">
                              <?php echo timezone_menu('UM5', 'xlarge'); ?>
                         </div>
                    </div>
                    
                    
                    <div class="clearfix">
                         <label for="agree-license">Do you agree to the terms of service?</label>
                         <div class="input">
                              
                              
                              <div class="tos-box">
                              <h3>For Schools</h3>

<p>TruantToday helps bring back hundreds of thousands of dollars in lost state and federal funding. In doing so, TruantToday has saved thousands of jobs and has helped create hundreds of arts programs in schools everywhere from New York to California. TruantToday will even help lower dropout rates and raise grades. Contact us via phone or email to find out how TruantToday can help your school or district.</p>

<h3>For Parents</h3>

<p>TruantToday instantly sends you a text and email message when your student cuts class, allowing you to always be updated on your child's behavior quickly, easily, and with no additional work needed on your part. In fact, by being more aware of your child's behavior, you can help them raise their grades by an average of 15%. Contact us to have us reach out to your school or district about using TruantToday.</p>

<h3>For Communities</h3>

<p>TruantToday helps lower crime in communities. Chronically-truant students have a 200% higher chance of dropping out, and have an 82% higher chance of committing a crime. Schools that use TruantToday have significantly lower numbers of chronically-truant students, and as a result, lower incidences of crime.</p>
                         </div>
                         
                         <label for="agree-license" style="width:auto;"><input type="checkbox" name="agree-license" id="agree-license" /> <span>Yes</span></label>
                         
                         
                         </div>
                    </div>
                    
                    <div class="actions">
                         <input type="submit" class="btn primary large" value="Create Your Account!" />
                    </div>
               </fieldset>
          <?php echo form_close(); ?>
          
          </div>
          
          <div class="span5 columns" style="width:275px;">
               <?php
               /*
               <div class="page-header"><h3>Thanks!</h3></div>
               
               <p>Thank you for using TruantToday! We protect your business and ensure that your data is both safe from others and still easy to use.</p>
               
               
               */
               ?>
               <div class="page-header"><h3>Share TruantToday with Others!</h3></div>
               
               <div class="content-page">
                    <p>We're glad you're choosing TruantToday. If you know another teacher who could benefit from TruantToday, <a href="<?=site_url('share')?>">let them know</a>.</p>
               </div>
               
               <div class="span6 " style="margin-top:30px; display:none;">
                    <div class="page-header"><h3 style="letter-spacing:-1px;"><a href="<?=site_url('about')?>">Learn About Who's Behind TruantToday</a></h3></div>
               </div>
               
               
          </div>
     </div>
     
</div>