<!DOCTYPE html>
<?php
$_serverRequest_ = $this->container->get(\Psr\Http\Message\ServerRequestInterface::class);
$_guard_ = $this->container->get(\Spiral\Security\GuardInterface::class);
?>
<stack:collect name="init" level="2"/>
<html lang="${lang|en}">
<head>
  <title>${baseTitle}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
  <script>
      window.csrfToken = "{!! $_serverRequest_->getAttribute("csrfToken") !!}";
  </script>
  <block:head/>
  <block:styles>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;500;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="/keeper/keeper.css"/>
  </block:styles>
  <stack:collect name="styles" level="2"/>
</head>
<body>
<block:body/>
<block:scripts>
  <script type="text/javascript" src="/toolkit/ie11.js"></script>
  <script type="text/javascript" src="/keeper/keeper.js"></script>
  <script type="text/javascript" src="/toolkit/toolkit.js"></script>
  <stack:collect name="scripts" level="2"/>
</block:scripts>
</body>
<hidden>${context}</hidden>
</html>
