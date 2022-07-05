<?php declare(strict_types=1);

// Pass in required credentials via _ENV[] variables.
// We bail out here if no credentials can be found!


if(getenv('CC_USER') === false)
  {
  die("ERROR: Env variable \"CC_USER\" missing!\n\n");
  }
if(getenv('CC_PASS') === false)
  {
  die("ERROR: Env variable \"CC_PASS\" missing!\n\n");
  }
if(getenv('CC_HOST') === false)
  {
  die("ERROR: Env variable \"CC_HOST\" missing!\n\n");
  }
