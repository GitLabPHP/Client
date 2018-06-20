<?php

require 'vendor/autoload.php';

$client = \Gitlab\Client::create('https://git.oxyshop.cz')
    ->authenticate('GZ_Sjjs2LESUW3kDzx4N', \Gitlab\Client::AUTH_HTTP_TOKEN);

$pager = new \Gitlab\ResultPager($client);

$oxidGroupId = 36;
$projects = $pager->fetchAll($client->groups(), 'projects', [$oxidGroupId, []]);

$pc = $mc = 0;
foreach ($projects as $id => $project) {
    $pc++;
    print_r("\n" . '------------------------------' . "\n");
    print_r('*' . $project['name_with_namespace'] . '*' . "\n");
    foreach ($client->api('merge_requests')->all($project['id'], ['state' => 'opened']) as $mr) {
        $mc++;
        $labels = implode(',', $mr['labels']);
        print_r("{$mr['title']}, Author: {$mr['author']['name']}, Assignee: {$mr['assignee']['name']}, Labels: {$labels}\n");
    }
}

print_r("Total OXID Projects: $pc, Total Open MR: $mc");
/*
$nowajaId = 5;
$tokens = $client->users()->userImpersonationTokens($nowajaId, ['state' => 'active']);

print_r($tokens);*/