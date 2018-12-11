<?php
require_once("config.php");
session_start();

function request($url, $method="GET", $params="", $headers=[]) {
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, $url);
  if ($method == "POST") {
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
  }

  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);


  $response = curl_exec($ch);

  curl_close($ch);

  $json = json_decode($response, true);

  return (json_last_error() == JSON_ERROR_NONE ? $json : false);
}

function api($method) {
  global $conf;
  return request($conf["jutgeUrl"]."competitions/".$conf["competitionName"].$method."?format=json");
}

function getusers($long=false) {
  $round1 = api("/round/1");
  $users = [];

  foreach ($round1["turns"][1]["matches"] as $match) {
    foreach ($match["players"] as $email => $player) {
      $explodedemail = explode("@", $email);
      if ($explodedemail[1] == "jutge.org") continue;
      $users[] = ($long ? $email : $explodedemail[0]);
    }
  }

  sort($users);

  return $users;
}

function getround($competition = [], $round) {
  if (isset($competition["current_round"]))
    $rounds = $competition["current_round"];
  else {
    $competition = api("/");
    if ($competition === false || !isset($competition["current_round"])) return false;
    $rounds = $competition["current_round"];
  }

  if ($competition["current_round"] < $round) return false;

  return api("/round/".$round);
}

function getuser($user) {
  $competition = api("/");
  if ($competition === false || !isset($competition["current_round"])) return false;

  $return = [];
  $return["matches"] = [];

  $lastround = 0;

  for ($i = 1; $i <= $competition["current_round"]; $i++) {
    if ($lastround < $i - 1) break;

    $round = getround($competition, $i);
    if ($round === false) return false;

    $return["matches"][$i] = [];
    $turns = count($round["turns"]);
    foreach ($round["turns"] as $j => $turn) {
      foreach ($turn["matches"] as $match) {
        $isplayinghere = false;
        $scores = [];
        $dummy = [];
        foreach ($match["players"] as $email => $player) {
          $scores[$email] = $player["score"];
          $dummy[$email] = (explode("@", $email)[1] == "jutge.org");
          if ($email === $user)
            $isplayinghere = true;
        }

        if ($isplayinghere) {
          $betterthan = -1;
          arsort($scores);
          $emails = array_keys($scores);
          $lastround = $i;
          $return["matches"][$i][$j] = $match;
          $pos = 0;
          foreach ($scores as $email => $score) {
            $pos++;
            $return["matches"][$i][$j]["players"][$email]["qualified"] = ($pos == 1 || ($j < $turns && $pos == 2) || ($j == $turns && (($pos == 3 && !$dummy[$emails[3]]) || ($pos == 2 && !($dummy[$emails[3]] && $dummy[$emails[2]])))));
          }
        }
      }
    }
  }

  if (count($return["matches"][count($return["matches"])]) == 0)
    unset($return["matches"][count($return["matches"])]);

  $return["alive"] = ($return["matches"][count($return["matches"])][count($return["matches"][count($return["matches"])])]["players"][$user]["qualified"]);
  $return["last_round"] = ($return["alive"] ? -1 : count($return["matches"]));

  return $return;
}
