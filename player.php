<?php
require_once("core.php");

if (!isset($_GET["player"])) {
  header("Location: index.php");
  exit();
}

$users = getusers(true);

if ($users === false)
  die("Houston, we've got a problem.");

$player = $_GET["player"];
$email = "";

$isplayer = false;
foreach ($users as $user)
  if ($player === substr($user, 0, strlen($player))) {
    $isplayer = true;
    $email = $user;
    break;
  }

if ($isplayer === false)
  die("This player doesn't exist.");

$data = getuser($email);

if ($data === false)
  die("Houston, we've got a problem.");
?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <title><?=htmlspecialchars($player)." - ".$conf["appName"]?></title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,600,700|Roboto+Mono:400">
    <link rel="stylesheet" href="node_modules/material-components-web/dist/material-components-web.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  </head>
  <body>
    <header class="mdc-top-app-bar">
      <div class="mdc-top-app-bar__row">
        <section class="mdc-top-app-bar__section mdc-top-app-bar__section--align-start">
          <a href="index.php" class="material-icons mdc-top-app-bar__navigation-icon">arrow_back</a>
          <span class="mdc-top-app-bar__title"><?=$conf["appName"]?></span>
        </section>
        <section class="mdc-top-app-bar__section mdc-top-app-bar__section--align-end" role="toolbar">
        </section>
      </div>
    </header>
    <div class="mdc-top-app-bar--fixed-adjust">
      <div class="content mdc-elevation--z4">
        <h2><?=htmlspecialchars($player)?></h2>
        <?php
        foreach ($data["matches"] as $i => $round) {
          echo "<h3>Ronda $i</h3><table class=\"rounds\">";
          foreach ($round as $j => $match) {
            $status = ($match["played"] == "true" ? ($match["players"][$email]["qualified"] ? "done" : "clear") : "timelapse");
            ?>
            <tr>
              <td><span class="material-icons icon <?=$status?>"><?=$status?></span></td>
              <td>Torn <?=$j?></td>
              <?php
              foreach ($match["players"] as $pemail => $p) {
                echo "<td class='player".($pemail === $email ? " me" : "").(($match["played"] != "true" || $match["players"][$pemail]["qualified"]) ? "" : " notqualified")."'><span class='playername' title='".htmlspecialchars(explode("@", $pemail)[0])."'>".htmlspecialchars($p["player_name"])."</span>".($match["played"] == "true" ? "<br><span class='score'>".(int)$p["score"]."</span>" : "")."</td>";
              }
              ?>
              <td><a href="<?=htmlspecialchars($match["url"])?>" target="_blank" class="mdc-icon-button material-icons">play_circle_outline</a></td>
            </tr>
            <?php
          }
          echo "</table>";
        }

        if (!$data["alive"]) {
          ?>
          <p style="text-align: center;">Malauradament, el jugador <b style="font-family: 'Roboto Mono', monospace;"><?=$player?></b> ha mort a la ronda <?=$data["last_round"]?>.</p>
          <?php
        }
        ?>
      </div>
    </div>
    <script src="node_modules/material-components-web/dist/material-components-web.min.js"></script>
    <script src="script.js"></script>
  </body>
</html>
