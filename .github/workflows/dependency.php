<?php

if (!$argv[1] || !$argv[2] || !$argv[3]) {
  test('No argument provided');
  exit(1);
}

$file = $argv[1];
$dir = $argv[2];
$command = $argv[3];
test("Processing file: $file");
test("Checking directory: $dir");

$shaDist = hash_file('sha256', '/dist/' . $file);
$shaCache = hash_file('sha256', '/cache/' . $file);
test("shaDist: $shaDist");
test("shaCache: $shaCache");

// check
if (!file_exists('/dist/' . $file)) {
  test("Could not find the dist file");
  exit;
} else {
  test("Found the dist file");
}

if ($shaDist === $shaCache) { // use cache

  test("=== Use cache === ");
  cmd("cp -R /cache/$dir/* /dist/$dir/");

} else {// rebuilt

  test("=== Build & cache === ");
  cmd("cd /dist; $command");
  cmd("cp -R /dist/$dir/* /cache/$dir/");
  cmd("cp /dist/$file /cache/$file");
}

function cmd($cmd)
{
  test("Doing cmd : $cmd");
  $output = shell_exec($cmd);
}

function test($string)
{
  echo $string . PHP_EOL;
}
