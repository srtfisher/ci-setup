<?php $this->load->view('shared/spotlight', array('title' => 'Plans and Pricing')); ?>

<!-- Content Text -->
<div class="container white-container extra-padding">
     <div class="row">
          <!-- Start Inner Content -->
               <h2 class="landing-page-super">Simple Pricing for Your School</h2>
               <span class="landing-page-super-sub">No tricks, gimmicks or magicians here.</span>
               
               <div class="pricing-number-container">
                    <div class="pricing-number-inner-container">
                         <span class="pricing-number-number"><a href="<?php echo site_url('signup'); ?>">$1</a></span>
                         <span class="pricing-number-sub-header">Per Student Per Year</span>
                         
                         <span class="pricing-detail">Unlimited Alerts</span>
                         <span class="pricing-detail">Unlimited Messages</span>
                         <span class="pricing-detail">Parent Center</span>
                         <span class="pricing-detail">30 Day Free Trial</span>
                         
                         <div class="alignCenter button-holder">
                              <p>
                                   <a href="<?php echo site_url('signup'); ?>" class="btn success large">Signup Now!</a>
                              </p>
                              
                              <p>
                                   <a href="<?=site_url('app/contact')?>" class="contact-us-link">Contact us with Any Questions</a>
                              </p>
                         </div>
                    </div>
               </div>
          <!-- End Inner Content -->
     </div>
</div>
<?php
/**
 * @ignore
**/
function tour_page_js()
{
     ?>
     var current_section      = 1;
     var section_id           = 1;
     
     $(document).ready(function () {
          $('div.tour-sidebar-item-link').click(function()
          {
               // The new section's ID
               section_id = $(this).attr('section-link');
               console.log(section_id+' '+current_section);
               console.log(section_id !== current_section);
               if( parseInt(section_id) !== parseInt(current_section))
               {
                    // Hide the old one
                    $('.tour-main-container[section-id="'+current_section+'"]').slideUp('fast'); //();
                    
                    // Show the new one
                    $('.tour-main-container[section-id="'+section_id+'"]').slideDown('fast');
                    
                    // Change the navigation item
                    $('div.tour-sidebar-item-link[section-link="'+current_section+'"]').removeClass('active-link');
                    $('div.tour-sidebar-item-link[section-link="'+section_id+'"]').addClass('active-link');
                    
                    // Set it for next time
                    current_section = section_id;
               }
          });
     });  
     <?php
}
// add_action('js_footer', 'tour_page_js');