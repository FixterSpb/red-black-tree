<?php
    require './Node.php';

    $tree = new Node(10);
    $tree->add(new Node(5));
    $tree->add(new Node(15));
    $tree->add(new Node(1));
    $tree->add(new Node(2));
    $tree->add(new Node(3));
    $tree->add(new Node(4));
    $tree->add(new Node(6));

//    var_dump($tree);
    header("Access-Control-Allow-Origin: *");
    echo json_encode($tree, JSON_FORCE_OBJECT);

