<?php
// Trims the starting and trailing white spaces and checks for html chars
function clean_data($data) {
  $data = trim($data);
  $data = htmlspecialchars($data);
  return $data;
}
