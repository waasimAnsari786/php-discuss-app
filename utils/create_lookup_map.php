<?php
function createLookupMap($data, $key, $value)
{
  $map = [];
  foreach ($data as $item) {
    $map[$item[$key]] = $item[$value];
  }
  return $map;
}
