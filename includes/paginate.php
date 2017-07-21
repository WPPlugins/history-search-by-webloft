<?php

// Paginerer trefflisten

// include the pagination class
require_once 'pagination.php';

// instantiate the pagination object
$pagination = new Zebra_Pagination();

// the number of total records is the number of records in the array
$pagination->records(count($treff));

// records per page
$pagination->records_per_page($wltreffperside);

// here's the magick: we need to display *only* the records for the current page
$treff = array_slice(
    $treff,
    (($pagination->get_page() - 1) * $wltreffperside),
    $wltreffperside,
	TRUE
); // TRUE to preserve array indexes

// render the pagination links
$wloutpaginate = '<div class="wl-paginate">' . $pagination->render(TRUE) . '</div>' . "\n\n";

?>
