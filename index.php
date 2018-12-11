<?php
require_once("core.php");

$competition = api("/");
$users = getusers();

if ($users === false)
  die("Houston, we've got a problem.");
?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <title><?=$conf["appName"]?></title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,600,700|Roboto+Mono:400">
    <link rel="stylesheet" href="node_modules/material-components-web/dist/material-components-web.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  </head>
  <body>
    <header class="mdc-top-app-bar">
      <div class="mdc-top-app-bar__row">
        <section class="mdc-top-app-bar__section mdc-top-app-bar__section--align-start">
          <span class="mdc-top-app-bar__title"><?=$conf["appName"]?></span>
        </section>
        <section class="mdc-top-app-bar__section mdc-top-app-bar__section--align-end" role="toolbar">
        </section>
      </div>
    </header>
    <div class="mdc-top-app-bar--fixed-adjust">
      <div class="mdc-layout-grid">
        <div class="mdc-layout-grid__inner">
          <div class="mdc-layout-grid__cell">
            <div class="mdc-card">
              <div class="card-content">
                <h2 class="title mdc-typography--headline6">Informació sobre jugadors</h2>
                <form action="player.php" method="GET">
                  <p>Sel·lecciona un jugador per veure informació sobre aquest:</p>
                  <p>
                    <div class="mdc-select" data-mdc-auto-init="MDCSelect">
                      <i class="mdc-select__dropdown-icon"></i>
                      <select name="player" class="mdc-select__native-control">
                        <option value="" disabled selected></option>
                        <?php
                        foreach ($users as $user) {
                          echo "<option value='$user'>$user</option>";
                        }
                        ?>
                      </select>
                      <label class="mdc-floating-label">Jugador</label>
                      <div class="mdc-line-ripple"></div>
                    </div>
                  </p>
                  <p><button class="mdc-button mdc-button--unelevated" data-mdc-auto-init="MDCRipple">Envia</button></p>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script src="node_modules/material-components-web/dist/material-components-web.min.js"></script>
    <script src="script.js"></script>
  </body>
</html>
