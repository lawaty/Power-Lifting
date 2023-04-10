<?php

// Define the Git repository and branch to pull from
$repo_url = 'https://github.com/lawaty/Time-Planner';
$branch = 'master';

// Define the path to the local repository
$repo_path = '../..';

// Execute the Git pull command
exec("cd {$repo_path} && git checkout {$branch} && git pull {$repo_url} {$branch} -X theirs", $output, $return_var);

// Check the return status of the Git pull command
if ($return_var !== 0) {
  // Something went wrong with the pull command
  logGit("Error: Git pull failed");
  exit;
}

// Output the results of the Git pull command
logGit("Git pull successful:\n [" . implode("\n", $output) . "]");

function logGit(string $msg): void
{
  $msg .= "[".date('h:i:s')."]";
  $filename = 'logs/got' . date('Y-m-d') . '.log';

  if (!file_exists($filename))
    $fhand = fopen($filename, "w");
  else
    $fhand = fopen($filename, "a");

  fwrite($fhand, $msg);
}
