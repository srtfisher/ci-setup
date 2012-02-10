<?php
// We still have to setup this file because it's called when you call on show_404() which is called enough ;)

// Default CSS
get_instance()->Core->load_default_assets();

// Header
get_instance()->Core->set_title('File not Found');
get_instance()->output->set_status_header('404');

// Views
get_instance()->load->view('shared/header');
get_instance()->load->view('404');
get_instance()->load->view('shared/footer');

/* End of file error_404.php */