<?php $this->load->view('shared/spotlight', array('title' => 'View '.$user_row->user_slug)); ?>

<!-- Content Text -->
<div class="container white-container extra-padding">
     <div class="row">
          <div class="span13 columns">
               <div class="page-header"><h2>View User <small><?php echo character_limiter($user_row->user_first_name . ' '. $user_row->user_last_name, 50); ?></small></h2></div>
               
               <?php
               if ($get_periods->num_rows() == 0) :
                    ?><div class="alert-message block-message warning">
        <p><strong>No school periods found!</strong></p></div><?php
               else :
                    foreach($get_periods->result() as $row) : ?>    
               <div class="padded-bottom-border">
                    <h3><a href="<?php echo site_url('periods/manage/'.$row->id); ?>"><?php echo $row->period_name; ?></a></h3>
                    <p>Period #<?php echo $row->id; ?> &mdash; <strong>Timing</strong> <?php echo date('g:i A', strtotime($row->period_start_time)); ?> &mdash; <?php echo date('g:i A', strtotime($row->period_end_time)); ?></p>
               </div>
               <?php
                    endforeach;
               endif;
               ?>
             
          </div>
          
          <!-- Right column -->
          <div class="span5 columns">

<p><a <?php if (! Perm::has_perk('periods.create')) { ?>href="javascript:void(o);"<?php } else{ ?>href="<?php echo site_url('periods/create/'.$school_row->id); ?>"<?php } ?> class="btn primary large <?php if (! Perm::has_perk('periods.create')) echo 'disabled'; ?>" <?php if (! Perm::has_perk('periods.create')) { echo 'title="You don\'t have permission to!"'; } ?>>Add a Period</a></p>

<?php //if ($school_row->id !== Auth::get('school_id')) : ?>
<h3>How do I see other schools?</h3>
<p>By default, we load the school you are assigned to. If you want to view other school's periods (if you have permission to), you can click on "View Periods" under the school's page listed <a href="<?php echo site_url('schools'); ?>">here</a>.</p>

<?php //endif; ?>

          </div>
     </div>
</div>